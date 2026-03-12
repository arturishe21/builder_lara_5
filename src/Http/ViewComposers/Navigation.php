<?php

namespace Vis\Builder\Http\ViewComposers;

use Illuminate\View\View;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Cms\Admin;

class Navigation
{
    public function compose(View $view)
    {
        $user = Sentinel::getUser();
        $menu =  app(Admin::class)->menu();

        $view->with(compact('user', 'menu'));
    }
}
