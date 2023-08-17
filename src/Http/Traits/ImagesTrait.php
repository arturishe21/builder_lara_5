<?php

namespace Vis\Builder\Http\Traits;

use Illuminate\Support\Facades\Config;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StudentT;
use Vis\Builder\Setting;

trait ImagesTrait
{
    public function getImg($width = '', $height = '', array $options = []): string
    {
        $img_res = $this->getImgPath($width, $height, $options);

        return  '<img src = "'.$img_res.'" title = "'.e($this->t('title')).'" alt = "'.e($this->t('title')).'">';
    }

    public function getImgLang($width = '', $height = '', $options = []): string
    {
        $img_res = $this->getImgPath($width, $height, $options, true);

        return  '<img src = "'.$img_res.'" title = "'.e($this->t('title')).'" alt = "'.e($this->t('title')).'">';
    }

    public function getImgPath($width = '', $height = '', $options = [], $lang = false): string
    {
        $picture = $this->picture;

        if (isset($options['image_title']) && $options['image_title']) {
            $name = $options['image_title'];
            $picture = $this->$name;
        }

        if ($lang) {
            $picture = $this->t('picture');
        }

        if (! $picture) {
            $picture = setting('no-foto');
        }

        $size = [];
        if ($width) {
            $size['w'] = $width;
        }

        if ($height) {
            $size['h'] = $height;
        }

        $params = array_merge($size, $options);

        return glide($picture, $params);
    }

    public function getOtherImg(string $nameField = 'additional_pictures', $paramImg = ''): ?array
    {
        if (! $this->$nameField) {
            return null;
        }

        $images = json_decode($this->$nameField);

        $imagesRes = [];
        foreach ($images as $imgOne) {
            if ($paramImg) {
                $imagesRes[] = glide($imgOne, $paramImg);
            } else {
                $imagesRes[] = '/'.$imgOne;
            }
        }

        return $imagesRes;
    }

    public function getOtherImgWatermark(string $nameField = 'additional_pictures', string $paramImg = ''): ?array
    {
        if (! $this->$nameField) {
            return null;
        }

        $images = json_decode($this->$nameField);
        $watermarkIsActive = config('builder.watermark.active');

        $imagesRes = [];
        foreach ($images as $imgOne) {

            $imagesRes[] = $paramImg
                ? $watermarkIsActive ? '/img/watermark/'.ltrim($imgOne, '/') : glide($imgOne, $paramImg)
                : '/'. $imgOne;
        }

        return $imagesRes;
    }

    public function getOtherImgWithOriginal(string $nameField = 'additional_pictures', $paramImg = '')
    {
        if (! $this->$nameField) {
            return;
        }

        $images = json_decode($this->$nameField);

        $imagesRes = [];
        foreach ($images as $imgOne) {
            if ($paramImg) {
                $imagesRes[$imgOne] = glide($imgOne, $paramImg);
            } else {
                $imagesRes[] = $imgOne;
            }
        }

        return $imagesRes;
    }

    public function getWatermark($width = '', $height = '', $options = [])
    {
        if (config('builder.watermark.active') && $this->picture) {
            return '/img/watermark/'.ltrim($this->picture, '/');
        } else {
            return $this->getImgPath($width, $height, $options);
        }
    }
}
