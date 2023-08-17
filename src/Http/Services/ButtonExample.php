<?php

namespace Vis\Builder\Http\Services;;

use Illuminate\Contracts\View\View;
use Vis\Builder\Interfaces\Button;

class ButtonExample extends ButtonBase implements Button
{
    public function show(): View
    {
        return view('admin::list.buttons.button_example');
    }
}