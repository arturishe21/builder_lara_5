<?php

namespace Vis\Builder\Definitions\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheResource
{
    public function getCacheKey()
    {
        return $this->cacheTag ?: $this->getNameDefinition();
    }

    public function clearCache()
    {
        Cache::tags($this->getCacheKey())->flush();
    }

}
