<?php

namespace Vis\Builder\Http\ViewComposers;

use Illuminate\View\View;
use Vis\Builder\Models\Language;

class Languages
{
    public function compose(View $view)
    {
        $languages = (new Language())->getLanguages();

        $view->with(compact( 'languages'));
    }
}
