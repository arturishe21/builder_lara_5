<?php

namespace Vis\Builder\Http\ViewComposers;

use Illuminate\View\View;

class NavigationBadge
{
    public function compose(View $view)
    {
        if(isset($view->menu['badge'])) {

            $badgeValue = $view->menu['badge']();

            $view->with(compact('badgeValue'));
        }
    }
}
