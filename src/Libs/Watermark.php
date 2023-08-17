<?php

namespace Vis\Builder\Libs;

use Intervention\Image\Filters\FilterInterface;
use Intervention\Image\Image;

class Watermark implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->widen(config('builder.watermark.width'))->insert(
            config('builder.watermark.path_watermark'),
            config('builder.watermark.position'),
            config('builder.watermark.x'),
            config('builder.watermark.y')
        );
    }
}
