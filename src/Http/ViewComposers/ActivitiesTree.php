<?php

namespace Vis\Builder\Http\ViewComposers;

use Illuminate\View\View;

class ActivitiesTree
{
    public function compose(View $view)
    {
        $type = $view->getData()['type'];
        $active = true;
        $caption = '';

        $view->with(compact('active', 'caption'));
    }
}
