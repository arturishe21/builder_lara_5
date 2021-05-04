<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use Vis\Builder\Tree as TreeBuilder;
use App\Models\MorphOne\Seo;

class Tree extends TreeBuilder
{
    public function seo()
    {
        return $this->morphOne(Seo::class, 'seo')->withDefault();
    }

    public static function getFirstDepthNodes()
    {
        return self::where('depth', '1')->get();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', '1');
    }

    public function scopePriorityAsc($query)
    {
        return $query->orderBy('lft', 'asc');
    }

    public function scopeTemplate($query, $template)
    {
        return $query->where('template', $template);
    }

    public function getDate()
    {
        return Util::getDate($this->created_at);
    }

    public function getUrl()
    {
        return geturl(parent::getUrl(), App::getLocale());
    }

}
