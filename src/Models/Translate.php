<?php

namespace Vis\TranslationsCMS;

use Illuminate\Database\Eloquent\Model;
use Yandex\Translate\Translator;
use Illuminate\Support\Facades\Cache;

class Translate extends Model
{
    protected $table = 'translations_cms';

    public $timestamps = false;

    protected $fillable = ['lang', 'translate'];

    public function generateTranslate($language, $phrase)
    {
        try {
            $languageDefault = config('builder.translations.cms.language_default');

            if ($language == $languageDefault) {
                return json_encode(['lang' => $language, 'text' => $phrase]);
            }

            $translator = new Translator(config('builder.translations.cms.api_yandex_key'));

            $translation = $translator->translate($phrase, $languageDefault . '-' . $language);

            if (isset($translation->getResult()[0])) {
                return json_encode(['lang' => $language, 'text' => $translation->getResult()[0]]);
            }
        } catch (\Yandex\Translate\Exception $e) {
            return $e->getMessage();
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
