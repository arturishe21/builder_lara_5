<?php

use Vis\Builder\Models\TranslationsCms;
use Vis\Builder\Models\TranslationsPhrasesCms;
use Vis\Builder\Models\Language;
use Illuminate\Support\Facades\Cache;
use App\Cms\Definitions\Settings;
use Illuminate\Support\Facades\App;
use Vis\Builder\Services\Translate;

if (! function_exists('defaultLanguage')) {

    function defaultLanguage() : string
    {
        try {
            $defaultLanguage = Language::getDefaultLanguage();

            if ($defaultLanguage) {
                return Language::getDefaultLanguage()->language;
            }

        } catch (\Exception $e) {
            config('app.locale');
        }

        return config('app.locale');
    }
}

if (! function_exists('languagesOfSite')) {

    function languagesOfSite()
    {
        return (new Language())->getLanguages()->pluck('language');
    }
}

if (! function_exists('setting')) {

    function setting(string $slug)
    {
        return Cache::tags('settings')->rememberForever($slug, function() use ($slug) {
            return (new Settings())->model()->getValue($slug);
        });
    }
}

if (! function_exists('settingForMail')) {

    function settingForMail(string $value)
    {
        return array_map('trim', explode(',', setting($value)));
    }
}

if (! function_exists('dr')) {
    /**
     * @param $array
     */
    function dr($array)
    {
        echo '<pre>';
        die(print_r($array));
    }
}

if (! function_exists('print_arr')) {
    /**
     * @param $array
     */
    function print_arr($array)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }
}

if (! function_exists('glide')) {

    function glide($source, array $options = [])
    {
        if (
            env('IMG_PLACEHOLDER', true)
            && (env('APP_ENV') === 'local' || env('APP_ENV') === 'testing')
        ) {
            $width = $options['w'] ?? 100;
            $height = $options['h'] ?? 100;

            return "//via.placeholder.com/{$width}x{$height}";
        }

        return (new Vis\Builder\Img())->get($source, $options);
    }
}

if (! function_exists('geturl')) {
    function geturl( string $url, $locale = false, array $attributes = []) : string
    {
        if (! $locale) {
            $locale = App::getLocale();
        }

        return LaravelLocalization::getLocalizedURL($locale, $url, $attributes);
    }
}

if (! function_exists('__cms')) {
    function __cms($phrase)
    {
        $thisLang = Cookie::get('lang_admin', config('builder.translations.cms.language_default'));

        $arrayTranslate = TranslationsPhrasesCms::fillCacheTrans();

        if (!isset($arrayTranslate[$phrase][$thisLang])) {
            if ($phrase) {
                (new TranslationsCms())->createNewTranslate($phrase);
            }
        }

        return $arrayTranslate[$phrase][$thisLang] ?? $phrase;
    }
}

if (! function_exists('__t')) {
    function __t(string $phrase, array $replacePhrase = []) : string
    {
        return (new Translate())->returnPhrase($phrase, $replacePhrase);
    }
}