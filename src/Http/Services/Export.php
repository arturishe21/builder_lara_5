<?php

namespace Vis\Builder\Services;

use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Export implements FromCollection, WithHeadings
{
    use Exportable;

    private $definition;

    public function __construct($definition)
    {
        $this->definition = (new $definition()) ;
    }

    public function headings(): array
    {
        foreach ($this->definition->head() as $field) {
            $fields[$field->getNameFieldInBd()] = $field->getNameFieldInBd();
        }

        $fields = Arr::only($fields, array_keys(request('b')));

        return array_values($fields);
    }

    public function collection()
    {
        $model = $this->definition->model();

        $results = $model::select(array_keys(request('b')));

        if (request('d')['from']) {
            $results->where('created_at', '>=', request('d')['from']);
        }

        if (request('d')['to']) {
            $results->where('created_at', '<=', request('d')['to']);
        }

        return $results->get();
    }

    public function show($list)
    {
        $class = addslashes(get_class($this));

        return view('admin::new.list.buttons.export', compact('list', 'class'));
    }
}
