<?php

namespace Vis\Builder\Services;

use Vis\Builder\Models\TranslationsPhrases;
use Illuminate\Support\Facades\Lang;

class Translate
{
    private $language;
    private $collectionTranslate;

    public function __construct()
    {
        $this->language = Lang::locale();
        $this->collectionTranslate = app('arrayTranslate');
    }

    public function returnPhrase(string $phrase, array $replacePhrase = []) : ?string
    {
        if (env('APP_ENV') == 'testing') {
            return $phrase;
        }

        $phrase = $this->checkExistsTranslate($phrase)
            ? $this->collectionTranslate[$phrase][$this->language]
            : TranslationsPhrases::generateTranslation($phrase, $this->language);

        return $this->replaceArrayPhrase($phrase, $replacePhrase);
    }

    private function checkExistsTranslate(string $phrase) : bool
    {
        return is_array($this->collectionTranslate) && array_key_exists($phrase, $this->collectionTranslate) && isset($this->collectionTranslate[$phrase][$this->language]);
    }

    private function replaceArrayPhrase($phrase, array $replacePhrase)
    {
        if (count($replacePhrase)) {
            $phrase = str_replace(array_keys($replacePhrase), array_values($replacePhrase), $phrase);
        }

        return $phrase;
    }
}
