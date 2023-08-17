<?php

namespace Vis\Builder\Models;

use Illuminate\Database\Eloquent\Model;
use Vis\Builder\Http\Traits\Rememberable;

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

    public static function scopeActive($query)
    {
        return $query->where('is_active', '1');
    }

    public static function scopeOrderPriority($query)
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

    public function getLanguages()
    {
        return $this->active()->orderPriority()
            ->rememberForever()->cacheTags(['languages'])
            ->get();
    }
}
