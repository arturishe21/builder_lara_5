<?php

namespace Vis\TranslationsCMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Vis\Builder\Libs\GoogleTranslateForFree;

class Translate extends Model
{
    protected $table = 'translations_cms';

    public $timestamps = false;

    protected $fillable = ['lang', 'translate'];

    public function createNewTranslate($phrase)
    {
        $languages = config('builder.translations.cms.languages');

        $newPhrase = Trans::create([
            'phrase' => $phrase
        ]);

        foreach ($languages as $lang => $value) {

            try {
                $translate = (new GoogleTranslateForFree())->translate('ru', $lang, $phrase, 2);
            } catch (\Exception $e) {
                $translate = $phrase;
            }

            $collection = [
                'lang' => $lang,
                'translate' => $translate
            ];

            $newPhrase->translationsPhrases()->create($collection);
        }

        Cache::tags('translations')->flush();
        Trans::fillCacheTrans();
    }
}
