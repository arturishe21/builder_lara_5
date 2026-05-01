<?php

namespace Vis\Builder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Vis\Builder\Libs\GoogleTranslateForFree;

class TranslationsCms extends Model
{
    protected $table = 'translations_cms';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function createNewTranslate(string $phrase): void
    {
        $languages = config('builder.translations.cms.languages');
        $thisLang = config('builder.translations.cms.language_default');

        $newPhrase = TranslationsPhrasesCms::create([
            'phrase' => $phrase
        ]);

        foreach ($languages as $lang => $value) {
            try {
                $translate = GoogleTranslateForFree::translate($thisLang, $lang, $phrase, 2);
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
