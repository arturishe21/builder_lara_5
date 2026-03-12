<?php

namespace Vis\Builder\Http\ViewComposers;

use Illuminate\View\View;
use App\Cms\Admin;

class LayoutDefault
{
    public function compose(View $view)
    {
        $admin = app(Admin::class);
        $skin = request()->cookie('skin') ?: 'smart-style-4';

        $view->with(compact('skin',  'admin'));
    }
}
