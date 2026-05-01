<?php

namespace Vis\Builder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class TranslationsPhrase extends Model
{
    protected $table = 'translations_phrases';
    protected $fillable = ['phrase'];
    public $timestamps = false;

    public function translations(): HasMany
    {
        return $this->hasMany(Translations::class, 'translations_phrase_id');
    }

    public function translationsLanguage(): array
    {
        return $this->translations()->pluck('translate', 'lang')->toArray();
    }

    public static function fillCacheTrans(): array
    {
        return Cache::rememberForever('translations', function () {
            return self::getArrayTranslation();
        });
    }

    public static function reCacheTrans(): void
    {
        Cache::forget('translations');
        self::fillCacheTrans();
    }

    private static function getArrayTranslation(): array
    {
        return TranslationsPhrase::with('translations')
            ->get()
            ->mapWithKeys(function ($phrase) {
                return [
                    $phrase->phrase => $phrase->translations
                        ->pluck('translate', 'lang')
                        ->toArray()
                ];
            })
            ->toArray();
    }
}
