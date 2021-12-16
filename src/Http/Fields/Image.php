<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class Image extends Field
{
    protected $path = '/storage/editor/fotos/';
    protected $isAutoTranslate = false;

    public function isTransparent()
    {
        $value = $this->getValue();

        return strpos($value, ".svg") || strpos($value, ".png") || strpos($value, ".gif");
    }

    public function getValueForList($definition)
    {
        $value = $this->getValue();

        if ($this->getLanguage()) {
            $language = $this->getLanguage()->first();
            $value = $this->getValueLanguage($language->language);
        }

        $img = glide($value, ['w' => 50, 'h' => 50]);
        $imgHover = glide($value, ['w' => 350, 'h' => 350]);

        return "<a class='screenshot' rel='{$imgHover}'><img src='{$img}'></a>";
    }

    public function uploadPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    public function selectWithUploadedImages($definition)
    {
        return $this->getImagesWithImageStorage($definition);
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
            $slugPage = isset($infoPage->title) ? Str::slug($infoPage->t('title')) : request('page_id');
            $fileName = $slugPage.'.'.$extension;
            $fullFileName = $this->path . $fileName;

            if (File::exists(public_path($fullFileName))) {
                $fileName = $slugPage.'_'.time().rand(1, 1000).'.'.$extension;
                $fullFileName = $this->path . $fileName;
            }
        }

        $status = $file->move(public_path($this->path), $fileName);

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
        if (! class_exists('\Vis\ImageStorage\Image')) {
            return;
        }

        $fileCmsPreview = strpos($fileName, '.svg') ?
            $fileName :
            str_replace($this->path, '', $link);

        $imgStorage = new \Vis\ImageStorage\Image();
        $imgStorage->file_folder = $this->path;
        $imgStorage->file_source = $fileName;
        $imgStorage->file_cms_preview = $fileCmsPreview;
        $imgStorage->save();
    }

    private function getImagesWithImageStorage($definition) : array
    {
        if (!class_exists('\Vis\ImageStorage\Image')) {
            return [
                'status' => 'success',
                'data'   => 'Не подключен пакет ImageStorage',
            ];
        }

        $list = \Vis\ImageStorage\Image::orderBy('created_at', 'desc');

        if (request('tag')) {
            $list->leftJoin('vis_tags2entities', 'id_entity', '=', 'vis_images.id')->where('entity_type', 'Vis\ImageStorage\Image')->where('id_tag', request('tag'));
        }

        if (request('gallary')) {
            $list->leftJoin('vis_images2galleries', 'id_image', '=', 'vis_images.id')->where('id_gallery', request('gallary'));
        }

        if (request('q')) {
            $list->where('vis_images.title', 'like', request('q').'%');
        }

        $list = $list->groupBy('vis_images.id')->paginate(18);

        $tags = \Vis\ImageStorage\Tag::where('is_active', 1)->orderBy('title', 'asc')->get();
        $galleries = \Vis\ImageStorage\Gallery::where('is_active', 1)->orderBy('title', 'asc')->get();

        return [
            'status' => 'success',
            'data'   => view('admin::tb.image_storage_list', compact('list', 'tags', 'galleries', 'definition'))->render(),
        ];
    }

}
