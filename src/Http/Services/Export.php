<?php

namespace Vis\Builder\Services;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Vis\Builder\Interfaces\Button;

class Export extends ButtonBase implements FromCollection, WithHeadings, Button
{
    use Exportable;

    public function headings(): array
    {
        foreach ($this->listing->head() as $field) {
            $fields[$field->getNameFieldInBd()] = $field->getNameFieldInBd();
        }

        $fields = Arr::only($fields, array_keys(request('b')));

        return array_values($fields);
    }

    public function collection()
    {
        $model = $this->listing->getDefinition()->model();

        $results = $model::select(array_keys(request('b')));

        if (request('d')['from']) {
            $results->where('created_at', '>=', request('d')['from']);
        }

        if (request('d')['to']) {
            $results->where('created_at', '<=', request('d')['to']);
        }

        return $results->get();
    }

    public function show():View
    {
        $class = addslashes(get_class($this));
        $list = $this->listing->head();

        return view('admin::new.list.buttons.export', compact('list', 'class'));
    }
}
