<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class Image extends Field
{
    protected $path = '/storage/editor/fotos/';

    public function isTransparent()
    {
        $value = $this->getValue();

        return strpos($value, ".svg") || strpos($value, ".png") || strpos($value, ".gif");
    }

    public function getValueForList($definition)
    {
        $img = glide($this->getValue(), ['w' => 50, 'h' => 50]);
        $imgHover = glide($this->getValue(), ['w' => 350, 'h' => 350]);

        return "<a class='screenshot' rel='{$imgHover}'><img src='{$img}'></a>";
    }

    public function selectWithUploadedImages($definition)
    {
      /*  if ($field->getAttribute('use_image_storage')) {
            return $this->getImagesWithImageStorage();
        }*/

        return $this->getImagesWithDefaultPath();
    }

    public function upload($definition)
    {
        $model = $definition->model();
        $file = request()->file('image');
        $width = 200;
        $height = 200;
        $extension = $this->getExtension($file->guessExtension());

        $rawFileName = md5_file($file->getRealPath()).'_'.time();
        $fileName = $rawFileName.'.'.$extension;

        $fullFileName = $this->path . $fileName;

        if ($model && request('page_id')) {
            $infoPage = $model::find(request('page_id'));
            $slugPage = isset($infoPage->title) ? Str::slug($infoPage->title) : request('page_id');
            $fileName = $slugPage.'.'.$extension;
            $fullFileName = $this->path . $fileName;

            if (File::exists(public_path($fullFileName))) {
                $fileName = $slugPage.'_'.time().rand(1, 1000).'.'.$extension;
                $fullFileName = $this->path . $fileName;
            }
        }

        $status = $file->move(ltrim($this->path, '/'), $fileName);

        $data = [];
        $data['sizes']['original'] = $fullFileName;

        $link = $extension == 'svg' ? $fullFileName : glide($fullFileName, ['w' => $width, 'h' => $height]);

        $this->saveInImageStore($fileName, $link);

        $returnView = request('type') == 'single_photo' ? 'admin::tb.html_image_single' : 'admin::tb.html_image';

        return [
            'data'       => $data,
            'status'     => $status,
            'link'       => $link,
            'short_link' => $fullFileName,
            'delimiter'  => ',',
            'html'       => view(
                $returnView,
                ['link'   => $link,
                    'data'   => $data,
                    'value'  => $fullFileName,
                    'name'   => request('ident'),
                    'width'  => $width,
                    'height' => $height,
                ]
            )->render(),
        ];
    }

    private function getExtension($guessExtension)
    {
        if ($guessExtension == 'html' || $guessExtension == 'txt') {
            return 'svg';
        }

        return $guessExtension;
    }

    private function saveInImageStore($fileName, $link)
    {
        return;

        if (! $this->getAttribute('use_image_storage') || ! class_exists('\Vis\ImageStorage\Image')) {
            return;
        }

        $fileCmsPreview = strpos($fileName, '.svg') ?
            $fileName :
            str_replace('/storage/editor/fotos/', '', $link);

        $imgStorage = new \Vis\ImageStorage\Image();
        $imgStorage->file_folder = '/storage/editor/fotos/';
        $imgStorage->file_source = $fileName;
        $imgStorage->file_cms_preview = $fileCmsPreview;
        $imgStorage->save();
    }

    private function getImagesWithDefaultPath()
    {
        $files = collect(File::files(public_path($this->path)))->sortBy(function ($file) {
            return filemtime($file);
        })->reverse();

        $page = (int) request('page') ?: 1;
        $onPage = 24;
        $slice = $files->slice(($page - 1) * $onPage, $onPage);

        $list = new \Illuminate\Pagination\LengthAwarePaginator($slice, $files->count(), $onPage);
        $list->setPath(url()->current());

        return [
            'status' => 'success',
            'data'   => view('admin::new.form.fields.partials.images_list', compact('list'))->render(),
        ];
    }

}
