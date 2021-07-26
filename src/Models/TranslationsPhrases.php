<?php

namespace Vis\Builder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Vis\Builder\Libs\GoogleTranslateForFree;

class TranslationsPhrases extends Model
{
    protected $table = 'translations_phrases';
    protected $fillable = ['phrase'];
    public $timestamps = false;

    public function translations()
    {
        return $this->hasMany(Translations::class, 'id_translations_phrase');
    }

    public function translationsLanguage()
    {
        return $this->translations()->pluck('translate', 'lang')->toArray();
    }

    /**
     * auto generate translation for function __t() if empty.
     *
     * @param string $phrase
     * @param strign $thisLang
     *
     * @return string
     */
    public static function generateTranslation($phrase, $thisLang)
    {
        if ($phrase && $thisLang) {
            $checkPresentPhrase = self::where('phrase', 'like', $phrase)->first();
            if (! $checkPresentPhrase) {
                $newPhrase = self::create(['phrase' => $phrase]);
                $languages = languagesOfSite();

                foreach ($languages as $language) {

                    try {
                        $translate = (new GoogleTranslateForFree())
                            ->translate(defaultLanguage(), $language, $phrase, 1);
                    } catch (\Exception $e) {
                        $translate = $phrase;
                    }

                    Translations::create(
                        [
                            'id_translations_phrase' => $newPhrase->id,
                            'lang'                   => $language,
                            'translate'              => $translate,
                        ]
                    );
                }

                self::reCacheTrans();
                $arrayTranslate = self::fillCacheTrans();

                return $arrayTranslate[$phrase][$thisLang] ?? 'error translation';
            }

            $translatePhrase = Translations::where('id_translations_phrase', $checkPresentPhrase->id)
                ->where('lang', 'like', $thisLang)->first();

            if ($translatePhrase) {
                return $translatePhrase->translate;
            }
        }
    }

    /**
     * filling cache translate.
     *
     * @return array
     */
    public static function fillCacheTrans()
    {
        if (Cache::get('translations')) {
            $arrayTranslate = Cache::get('translations');
        } else {
            $arrayTranslate = self::getArrayTranslation();
            Cache::forever('translations', $arrayTranslate);
        }

        return $arrayTranslate;
    }

    /** recache translate.
     *
     * @return void
     */
    public static function reCacheTrans()
    {
        Cache::forget('translations');
        self::fillCacheTrans();
    }

    /**
     * get array all phrase translation.
     *
     * @return array
     */
    private static function getArrayTranslation()
    {
        $translationsGet = DB::table('translations_phrases')
            ->leftJoin('translations', 'translations.id_translations_phrase', '=', 'translations_phrases.id')
            ->get(['translate', 'lang', 'phrase']);

        $arrayTranslate = [];
        foreach ($translationsGet as $el) {
            $el = (array) $el;
            $arrayTranslate[$el['phrase']][$el['lang']] = $el['translate'];
        }

        return $arrayTranslate;
    }
}
