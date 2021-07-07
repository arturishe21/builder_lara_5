<?php

use Vis\Builder\Models\TranslationsCms;
use Vis\Builder\Models\TranslationsPhrasesCms;
use Vis\Builder\Models\Language;
use Illuminate\Support\Facades\Cache;

if (! function_exists('defaultLanguage')) {

    function defaultLanguage()
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
            return (new \App\Cms\Definitions\Settings())->model()->getValue($slug);
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

if (! function_exists('remove_bom')) {
    /**
     * @param $val
     *
     * @return bool|string
     */
    function remove_bom($val)
    {
        if (substr($val, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
            $val = substr($val, 3);
        }

        return $val;
    }
}

if (! function_exists('glide')) {
    /**
     * @param $source
     * @param array $options
     *
     * @return mixed|string
     */
    function glide($source, $options = [])
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

if (! function_exists('filesize_format')) {
    /**
     * @param $bytes
     *
     * @return string
     */
    function filesize_format($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 1, '.', '').' Gb';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 1, '.', '').' Mb';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 1, '.', '').' Kb';
        } elseif ($bytes > 1) {
            $bytes = $bytes.' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes.' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}

/*
 * @param $url
 * @param bool $locale
 * @param array $attributes
 * @return false|string
 */
if (! function_exists('geturl')) {
    function geturl($url, $locale = false, $attributes = [])
    {
        if (! $locale) {
            $locale = App::getLocale();
        }

        return LaravelLocalization::getLocalizedURL($locale, $url, $attributes);
    }
}

/*
 * @param $phrase
 * @return mixed
 */
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

/*
 * get realy ip user
 *
 * @return string
 */
if (! function_exists('getIp')) {
    function getIp()
    {
        if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }
}

/*
 * @param $tree
 * @param $node
 * @param array $slugs
 * @return string
 */
if (! function_exists('recurseMyTree')) {
    function recurseMyTree($tree, $node, &$slugs = [])
    {
        if (! $node['parent_id']) {
            return $node['slug'];
        }

        $slugs[] = $node['slug'];
        $idParent = $node['parent_id'];
        if ($idParent) {
            $parent = $tree[$idParent];
            recurseMyTree($tree, $parent, $slugs);
        }

        return implode('/', array_reverse($slugs));
    }
}

/*
 * Returns entire string with current locale postfix, ex. string_ua
 *
 * @param  string
 * @return string
 */
if (! function_exists('getWithLocalePostfix')) {
    function getWithLocalePostfix($string)
    {
        $currentLocale = LaravelLocalization::getCurrentLocale();

        return $currentLocale == LaravelLocalization::getDefaultLocale() ? $string : $string.'_'.$currentLocale;
    }
}

if (! function_exists('getLocalePostfix')) {
    function getLocalePostfix($locale = null)
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }

        $languages = config('translations.config.languages');

        if (is_array($languages)) {
            foreach ($languages as $language) {
                if ($language['caption'] === $locale) {
                    return $language['postfix'];
                }
            }
        }

        return '';
    }
}

if (! function_exists('__t')) {
    function __t($phrase, array $replacePhrase = [])
    {
        if (env('APP_ENV') == 'testing') {
            return $phrase;
        }

        $thisLang = \Illuminate\Support\Facades\Lang::locale();
        $arrayTranslate = app('arrayTranslate');

        if (is_array($arrayTranslate) && array_key_exists($phrase, $arrayTranslate) && isset($arrayTranslate[$phrase][$thisLang])) {
            $phrase = $arrayTranslate[$phrase][$thisLang];
        } else {
            $phrase = \Vis\Builder\Models\TranslationsPhrases::generateTranslation($phrase, $thisLang);
        }

        if (count($replacePhrase)) {
            $phrase = str_replace(array_keys($replacePhrase), array_values($replacePhrase), $phrase);
        }

        return $phrase;
    }
}

if (! function_exists('cmp')) {
    function cmp($a, $b)
    {
        if ($a == $b) {
            return 0;
        }

        return (strlen($a) < strlen($b)) ? -1 : 1;
    }
}

if (!function_exists('parseIfJson')) {
    function parseIfJson(?string $data): ?string
    {
        if (!preg_match('~^{.*?}~', $data)) {
            return $data;
        }

        try {

            $data = json_decode($data, true);

            return $data['ua'] ?? null;

        } catch (Throwable $exception) {
            return null;
        }
    }
}

