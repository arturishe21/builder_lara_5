<?php

namespace Vis\Builder\Libs;

use Intervention\Image\Laravel\Facades\Image;

class Img
{
    private $size;
    private $nameFile;
    private $picturePath;
    private $pathFolder;
    private $width = null;
    private $height = null;
    private $quality = 90;

    public function get($source, $options)
    {
        if (! $source) {
            return;
        }

        $this->setOptions($options);
        $source = '/'.ltrim($source, '/');
        $sourceArray = pathinfo($source);

        if (!$this->checkFileCorrect($sourceArray)) {
            return false;
        }

        $filename = $sourceArray['filename'];
        $extension = $sourceArray['extension'];
        $dirname = $sourceArray['dirname'];

        $this->nameFile = $this->quality == 90 ?
                $filename.'.'.$extension :
                $filename.'_'.$this->quality.'.'.$extension;

        $this->pathFolder = $dirname.'/'.$this->size;
        $this->picturePath = $this->pathFolder.'/'.$this->nameFile;

        if ($extension == 'svg') {
            return $source;
        }

        if (self::checkExistPicture()) {
            return $this->picturePath;
        }

        try {
            $img = Image::read(public_path($source));

            if (config('builder.watermark.active') && file_exists(config('builder.watermark.path'))) {
                $img->insert(
                    config('builder.watermark.path'),
                    config('builder.watermark.position'),
                    config('builder.watermark.x'),
                    config('builder.watermark.y')
                );
            }

            $this->createRatioImg($img, $options);

            @mkdir(public_path($this->pathFolder));

            $pathSmallImg = public_path('/' . $this->picturePath);
            $img->save($pathSmallImg, $this->quality);

            OptmizationImg::run($this->picturePath);

            return $this->picturePath;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function checkFileCorrect($sourceArray): bool
    {
       return !isset($sourceArray['extension']) || !isset($sourceArray['dirname']) ? false : true;
    }

    protected function setOptions(array $options): void
    {
        $this->quality = $options['quality'] ?? $this->quality;
        $this->height = $options['h'] ?? $this->height;
        $this->width = $options['w'] ?? $this->width;

        if ($this->height === null) {
            $this->size = $this->width.'x0';
        } elseif ($this->width === null) {
            $this->size = '0x'.$this->height;
        } else {
            $this->size = $this->width.'x'.$this->height;
        }
    }

    protected function createRatioImg($img, array $options)
    {
        if (isset($options['fit']) && $options['fit'] == 'crop') {
            $img->fit(
                $this->width,
                $this->height,
                function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            );

            return;
        }

        $img->resize(
            $this->width,
            $this->height,
            function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            }
        );
    }

    protected function checkExistPicture(): bool
    {
        return file_exists(public_path($this->picturePath));
    }
}
