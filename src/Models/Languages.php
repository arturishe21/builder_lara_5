<?php

namespace Vis\Builder\Models;

use Illuminate\Database\Eloquent\Model;
use Vis\Builder\Helpers\Traits\Rememberable;

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
    }

    public static function scopeActive($query)
    {
        return $query->where('is_active', '1');
    }

    public static function scopeOrderPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    public function getName()
    {
        return $this->supportedLocales[$this->language]['name'] ?? '';
    }

    public function supportedLocales()
    {
        $result = [];

        foreach ($this->supportedLocales as $key => $info) {
            $result[$key] = $info['name'];
        }

        return $result;
    }

    public static function getDefaultLanguage()
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
