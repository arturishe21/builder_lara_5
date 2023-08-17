<?php

namespace Vis\Builder\Http\Definitions\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheResource
{
    public function getCacheKey(): string
    {
        return $this->cacheTag ?: $this->getNameDefinition();
    }

    public function clearCache(): void
    {
        Cache::tags($this->getCacheKey())->flush();
    }

}
