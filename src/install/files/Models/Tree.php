<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
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

    public function scopeActive($query): self
    {
        return $query->where('is_active', '1');
    }

    public function scopePriorityAsc($query): self
    {
        return $query->orderBy('lft', 'asc');
    }

    public function scopeTemplate($query, $template): self
    {
        return $query->where('template', $template);
    }

    public function getUrl(): string
    {
        return geturl(parent::getUrl(), App::getLocale());
    }

}
