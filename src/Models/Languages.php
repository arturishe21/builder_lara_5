<?php

namespace Vis\Builder\Models;

use App\Models\BaseModel;

class Language extends BaseModel
{
    protected $table = 'languages';
    protected $fillable = [];
    public $timestamps = false;
    private $supportedLocales;

    public function __construct()
    {
        $this->supportedLocales = config('laravellocalization.supportedLocales');
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
