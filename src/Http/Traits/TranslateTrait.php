<?php

namespace Vis\Builder\Helpers\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

trait TranslateTrait
{
    public function t($ident)
    {
        $ident = $this->tField($ident);

        return $this->$ident;
    }

    protected function tField($ident)
    {
        $lang = App::getLocale();
        $defaultLocale = config('translations.config.def_locale');
        if ($lang != $defaultLocale && $defaultLocale) {
            $ident = $ident.'_'.$lang;
        }

        return $ident;
    }
}
