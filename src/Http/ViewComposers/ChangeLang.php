<?php

namespace Vis\Builder\Http\ViewComposers;

use Illuminate\View\View;

class ChangeLang
{
    public function compose(View $view)
    {
        $languages = config("builder.translations.cms.languages");
        $thisLang = request()->cookie('lang_admin') ?: array_key_first(config('builder.translations.cms.languages'));

        $view->with(compact( 'languages', 'thisLang'));
    }
}
