<?php

namespace Vis\Builder\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Vis\Builder\Http\Traits\Rememberable;
use Illuminate\Database\Eloquent\Builder;

class Language extends Model
{
    use Rememberable;

    protected $table = 'languages';
    protected $fillable = [];
    public $timestamps = false;
    private $supportedLocales;

    public function __construct()
    {
        $this->supportedLocales = config('laravellocalization.supportedLocales');
        parent::__construct();
    }

    public static function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', '1');
    }

    public static function scopeOrderPriority(Builder $query): Builder
    {
        return $query->orderBy('priority', 'asc');
    }

    public function getName(): string
    {
        return $this->supportedLocales[$this->language]['name'] ?? '';
    }

    public function supportedLocales(): array
    {
        $result = [];

        foreach ($this->supportedLocales as $key => $info) {
            $result[$key] = $info['name'];
        }

        return $result;
    }

    public static function getDefaultLanguage(): self
    {
        return self::active()->orderPriority()
            ->rememberForever()->cacheTags(['languages'])
            ->first();
    }

    public function getLanguages(): Collection
    {
        return $this->active()->orderPriority()
            ->rememberForever()->cacheTags(['languages'])
            ->get();
    }
}
