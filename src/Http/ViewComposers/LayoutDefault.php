<?php

namespace Vis\Builder\Http\ViewComposers;

use Illuminate\View\View;

class LayoutDefault
{
    public function compose(View $view)
    {
        $admin = new \App\Cms\Admin();

        $skin = request()->cookie('skin') ?: 'smart-style-4';

        $view->with(compact('skin',  'admin'));
    }
}
