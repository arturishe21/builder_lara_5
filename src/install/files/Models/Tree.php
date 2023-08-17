<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Kalnoy\Nestedset\QueryBuilder;
use Vis\Builder\Models\Tree as TreeBuilder;
use App\Models\MorphOne\Seo;

class Tree extends TreeBuilder
{
    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seo')->withDefault();
    }

    public static function getFirstDepthNodes(): Collection
    {
        return self::where('depth', '1')->get();
    }

    public function scopeActive($query): QueryBuilder
    {
        return $query->where('is_active', '1');
    }

    public function scopePriorityAsc($query): QueryBuilder
    {
        return $query->orderBy('lft', 'asc');
    }

    public function scopeTemplate($query, $template): QueryBuilder
    {
        return $query->where('template', $template);
    }
    public function getUrl(): string
    {
        return geturl(parent::getUrl(), App::getLocale());
    }

}
