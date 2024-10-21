<?php

namespace Vis\Builder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Vis\Builder\Libs\GoogleTranslateForFree;

class TranslationsPhrases extends Model
{
    protected $table = 'translations_phrases';
    protected $fillable = ['phrase'];
    public $timestamps = false;

    public function translations(): HasMany
    {
        return $this->hasMany(Translations::class, 'id_translations_phrase');
    }

    public function translationsLanguage(): array
    {
        return $this->translations()->pluck('translate', 'lang')->toArray();
    }

    public static function generateTranslation(string $phrase, string $thisLang): string
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

    public static function fillCacheTrans(): array
    {
        if (Cache::get('translations')) {
            $arrayTranslate = Cache::get('translations');
        } else {
            $arrayTranslate = self::getArrayTranslation();
            Cache::forever('translations', $arrayTranslate);
        }

        return $arrayTranslate;
    }

    public static function reCacheTrans(): void
    {
        Cache::forget('translations');
        self::fillCacheTrans();
    }

    private static function getArrayTranslation(): array
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
