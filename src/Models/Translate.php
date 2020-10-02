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

    public function generateTranslate($language, $phrase)
    {
        try {
            $langDef = config('builder.translations.cms.language_default');

            if ($langDef == $language || !$phrase) {
                return json_encode(['lang' => $language, 'text' => $phrase]);
            }

            $result = (new GoogleTranslateForFree())->translate($langDef, $language, $phrase, 2);

            return json_encode(['lang' => $language, 'text' => $result]);

        } catch (\Exception $e) {
            return json_encode(['lang' => $language, 'text' => $phrase]);
        }
    }

    public function createNewTranslate($phrase)
    {
        $languages = config('builder.translations.cms.languages');

        $newPhrase = Trans::create([
            'phrase' => $phrase
        ]);

        foreach ($languages as $slug => $value) {

            $collection = json_decode($this->generateTranslate($slug, $phrase), true);
            $collection['translate'] = $collection['text'];
            unset($collection['text']);

            $newPhrase->translationsPhrases()->create($collection);
        }

        Cache::tags('translations')->flush();
        Trans::fillCacheTrans();
    }
}
