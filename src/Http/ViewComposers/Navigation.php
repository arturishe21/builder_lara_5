<?php

namespace Vis\Builder\Http\ViewComposers;

use Illuminate\View\View;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class Navigation
{
    public function compose(View $view)
    {
        $user = Sentinel::getUser();
        $menu =  (new \App\Cms\Admin())->menu();

        $view->with(compact('user', 'menu'));
    }
}
