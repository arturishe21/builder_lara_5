<?php

namespace Vis\Builder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TranslationsPhrasesCms extends Model
{
    protected $table = 'translations_phrases_cms';
    protected $fillable = ['phrase'];
    public $timestamps = false;

    public function translations(): HasMany
    {
        return $this->hasMany(TranslationsCms::class, 'translations_phrases_cms_id');
    }

    public function getTrans(): array
    {
        return $this->translations()->pluck('translate', 'lang')->toArray();
    }

    public static function fillCacheTrans(): array
    {
        if (Cache::tags('translations')->has('translations_cms')) {
            $arrayTranslate = Cache::tags('translations')->get('translations_cms');
        } else {
            $translationsGet = DB::table('translations_phrases_cms')->leftJoin(
                'translations_cms',
                'translations_cms.translations_phrases_cms_id',
                '=',
                'translations_phrases_cms.id'
            )
                ->get(['translate', 'lang', 'phrase']);

            $arrayTranslate = [];
            foreach ($translationsGet as $el) {
                $el = (array) $el;
                $arrayTranslate[$el['phrase']][$el['lang']] = $el['translate'];
            }
            Cache::tags('translations')->forever('translations_cms', $arrayTranslate);
        }

        return $arrayTranslate;
    }

    public static function reCacheTrans(): void
    {
        Cache::tags('translations')->flush();
        self::fillCacheTrans();
    }
}
