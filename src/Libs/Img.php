<?php

namespace Vis\Builder\Libs;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Illuminate\Support\Facades\File;

class Img
{
    private string $size;
    private string $nameFile;
    private string $picturePath;
    private string $pathFolder;
    private ?int $width = null;
    private ?int $height = null;
    private int $quality = 90;

    public function get(string $source, array $options)
    {
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

        if ($extension === 'svg') {
            return $source;
        }

        if ($this->checkExistPicture()) {
            return $this->picturePath;
        }

        try {
            $manager = new ImageManager(new Driver());
            $img = $manager->read(public_path($source));

            if (config('builder.watermark.active') && file_exists(config('builder.watermark.path'))) {
                $img->place(
                    config('builder.watermark.path'),
                    config('builder.watermark.position'),
                    config('builder.watermark.x'),
                    config('builder.watermark.y')
                );
            }

            $this->createRatioImg($img, $options);

            if (!File::isDirectory(public_path($this->pathFolder))) {
                mkdir(public_path($this->pathFolder));
            }

            $pathSmallImg = public_path('/' . $this->picturePath);
            $img->save($pathSmallImg, $this->quality);

            OptmizationImg::run($this->picturePath);

            return $this->picturePath;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function checkFileCorrect(array $sourceArray): bool
    {
        return !(!isset($sourceArray['extension']) || !isset($sourceArray['dirname']));
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

    protected function createRatioImg($img, array $options): void
    {
        $img->scale($this->width, $this->height);
    }

    protected function checkExistPicture(): bool
    {
        return file_exists(public_path($this->picturePath));
    }
}
