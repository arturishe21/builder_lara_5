<?php

namespace Vis\Builder\Http\ViewComposers;

use Illuminate\View\View;

class ChangeLang
{
    public function compose(View $view)
    {
        $languages = config("builder.translations.cms.languages");
        $thisLang = request()->cookie('language_cms') ?: config('builder.translations.cms.language_default');

        $view->with(compact( 'languages', 'thisLang'));
    }
}
