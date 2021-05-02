<?php

namespace Vis\Builder\Services;

use Illuminate\Contracts\View\View;
use Vis\Builder\Interfaces\Button;

class Import extends ButtonBase implements Button
{
    public function show():View
    {
        $nameDefinition = mb_strtolower(class_basename($this->listing->getDefinition()));

        return view('admin::list.buttons.import', compact( 'nameDefinition'));
    }
}