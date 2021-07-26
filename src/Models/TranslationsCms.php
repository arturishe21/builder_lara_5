<?php

namespace Vis\Builder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Vis\Builder\Libs\GoogleTranslateForFree;

class TranslationsCms extends Model
{
    protected $table = 'translations_cms';

    public $timestamps = false;

    protected $fillable = ['lang', 'translate', 'translations_phrases_cms_id'];

    public function createNewTranslate($phrase)
    {
        $languages = config('builder.translations.cms.languages');

        $newPhrase = TranslationsPhrasesCms::create([
            'phrase' => $phrase
        ]);

        foreach ($languages as $lang => $value) {

            try {
                $translate = (new GoogleTranslateForFree())->translate('ru', $lang, $phrase, 2);
            } catch (\Exception $e) {
                $translate = $phrase;
            }

            $newPhrase->translations()->create([
                'lang' => $lang,
                'translate' => $translate
            ]);
        }

        Cache::tags('translations')->flush();
        TranslationsPhrasesCms::fillCacheTrans();
    }
}
