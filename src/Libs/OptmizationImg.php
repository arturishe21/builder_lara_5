<?php

namespace Vis\Builder\Libs;

class OptmizationImg
{
    public static function run(string $pathImg): void
    {
        $infoImg = new \SplFileInfo($pathImg);
        $fullPathPicture = public_path($pathImg);

        if (config('builder.optimization_img.active')) {
            $commandPng = config('builder.optimization_img.png_path');
            $commandJpg = config('builder.optimization_img.jpg_path');

            try {
                if ($infoImg->getExtension() == 'png') {
                    $commandPng = str_replace('[file]', $fullPathPicture, $commandPng);
                    exec($commandPng, $res);
                } elseif ($infoImg->getExtension() == 'jpg' || $infoImg->getExtension() == 'jpeg') {
                    $commandJpg = str_replace('[file]', $fullPathPicture, $commandJpg);

                    exec($commandJpg, $res);
                }
            } catch (\Exception $e) {
            }
        }

        if (config('builder.optimization_img.webp_optimize')) {
            try {
                $newFile = str_replace(['.png', '.jpg', '.jpeg'], '.webp', $fullPathPicture);

                $command = 'cwebp -q 80 '.$fullPathPicture.' -o '.$newFile;

                exec($command, $res);
            } catch (\Exception $e) {
            }
        }
    }
}
