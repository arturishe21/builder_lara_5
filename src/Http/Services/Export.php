<?php

namespace Vis\Builder\Services;

use Maatwebsite\Excel\Concerns\Exportable;
use Vis\Builder\Interfaces\Button;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class Export extends ButtonBase implements Button, FromView
{
    use Exportable;

    public function view(): View
    {
        $listingRecords = $this->listing->getDefinition()->getListingForExel();

        return view('admin::exel', [
            'head' => $this->listing->head(),
            'items' => $listingRecords,
        ]);
    }

    public function show():View
    {
        $class = addslashes(get_class($this));
        $list = $this->listing->head();

        return view('admin::list.buttons.export', compact('list', 'class'));
    }

}
