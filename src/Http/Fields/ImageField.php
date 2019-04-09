<?php

namespace Vis\Builder\Fields;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use Vis\Builder\Facades\Jarboe;

class ImageField extends AbstractField
{
    public function isEditable()
    {
        return true;
    }

    public function getListValue($row)
    {
        if ($this->hasCustomHandlerMethod('onGetListValue')) {
            $res = $this->handler->onGetListValue($this, $row);

            if ($res) {
                return $res;
            }
        }

        if ($this->getAttribute('is_multiple')) {
            return $this->getListMultiple($row);
        }

        return $this->getListSingle($row);
    }

    private function getListSingle($row)
    {
        $pathPhoto = $this->getValue($row);

        if (!$pathPhoto) {
            return '';
        }

        return view('admin::fields.image.list_single', compact('pathPhoto'))->render();
    }

    private function getListMultiple($row)
    {
        if (!$this->getValue($row)) {
            return '';
        }

        $images = json_decode($this->getValue($row), true);

        return view('admin::fields.image.list_multiple', compact('images'))->render();
    }

    public function onSearchFilter(Builder $db, $value)
    {
        $db->where($this->getFieldName(), 'LIKE', '%'.$value.'%');
    }

    public function getTabbedEditInput($row = [])
    {
        if ($this->hasCustomHandlerMethod('onGetTabbedEditInput')) {
            $res = $this->handler->onGetTabbedEditInput($this, $row);

            if ($res) {
                return $res;
            }
        }

        $type = $this->getAttribute('type');

        $input = view('admin::tb.tab_input_'.$type);
        $input->value = $this->getValue($row);
        $input->name = $this->getFieldName();
        $input->rows = $this->getAttribute('rows');
        $input->caption = $this->getAttribute('caption');
        $input->tabs = $this->getPreparedTabs($row);
        $input->is_multiple = $this->getAttribute('is_multiple');
        $input->delimiter = $this->getAttribute('delimiter');
        $input->width = $this->getAttribute('img_width', 200);
        $input->height = $this->getAttribute('img_height', 200);
        $input->chooseFromUploaded = $this->getAttribute('choose_from_uploaded', true);

        return $input->render();
    }

    protected function getPreparedTabs($row)
    {
        $tabs = $this->getAttribute('tabs');

        foreach ($tabs as &$tab) {
            $tab['value'] = $this->getValue($row, $tab['postfix']);
        }

        return $tabs;
    }

    public function getEditInput($row = [])
    {
        if ($this->hasCustomHandlerMethod('onGetEditInput')) {
            $res = $this->handler->onGetEditInput($this, $row);
            if ($res) {
                return $res;
            }
        }

        $input = view('admin::tb.input_image_upload');
        $input->value = $this->getValue($row);
        $input->source = json_decode($this->getValue($row), true);
        $input->name = $this->getFieldName();
        $input->caption = $this->getAttribute('caption');
        $input->is_multiple = $this->getAttribute('is_multiple');
        $input->delimiter = $this->getAttribute('delimiter');
        $input->width = $this->getAttribute('img_width', 200);
        $input->height = $this->getAttribute('img_height', 200);
        $input->chooseFromUploaded = $this->getAttribute('choose_from_uploaded', true);
        $input->baseName = $this->getFieldName();

        return $input->render();
    }

    public function doUpload($file)
    {
        $model = $this->definition['options']['model'];

        $this->checkSizeFile($file);

        $extension = $this->getExtension($file->guessExtension());

        $rawFileName = md5_file($file->getRealPath()).'_'.time();
        $fileName = $rawFileName.'.'.$extension;

        $destinationPath = 'storage/editor/fotos/';

        if ($model && request('page_id')) {
            $infoPage = $model::find(request('page_id'));
            $slugPage = isset($infoPage->title) ? Jarboe::urlify(strip_tags($infoPage->title)) : request('page_id');
            $fileName = $slugPage.'.'.$extension;
            if (File::exists($destinationPath.$fileName)) {
                $fileName = $slugPage.'_'.time().rand(1, 1000).'.'.$extension;
            }
        }

        $status = $file->move($destinationPath, $fileName);

        $data = [];
        $data['sizes']['original'] = $destinationPath.$fileName;

        $width = $this->getAttribute('img_width', 200);
        $height = $this->getAttribute('img_height', 200);

        $link = $extension == 'svg' ? $destinationPath.$fileName
                                    : glide($destinationPath.$fileName, ['w' => $width, 'h' => $height]);

        $this->saveInImageStore($fileName, $link);

        $returnView = request('type') == 'single_photo' ? 'admin::tb.html_image_single' : 'admin::tb.html_image';

        $response = [
            'data'       => $data,
            'status'     => $status,
            'link'       => $link,
            'short_link' => $destinationPath.$fileName,
            'delimiter'  => ',',
            'html'       => view(
                $returnView,
                ['link'   => $link,
                 'data'   => $data,
                 'value'  => $destinationPath.$fileName,
                 'name'   => request('ident'),
                 'width'  => $width,
                 'height' => $height,
                ]
            )->render(),
        ];

        return $response;
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

    private function checkSizeFile($file)
    {
        if (! $this->getAttribute('limit_mb')) {
            return;
        }

        $limitMb = $this->getAttribute('limit_mb') * 1000000;

        if ($file->getSize() > $limitMb) {
            app()->abort(500, 'Ошибка загрузки файла. Файл больше чем '.$this->getAttribute('limit_mb').' МБ');
        }
    }

    public function prepareQueryValue($value)
    {
        if (!$value) {
            return '';
        }

        return $value;
    }

    public function multiple(bool $is = true)
    {
        $this->attributes['is_multiple'] = $is;

        return $this;
    }

    public function upload(bool $is = true)
    {
        $this->attributes['is_upload'] = $is;

        return $this;
    }

    public function remote(bool $is = true)
    {
        $this->attributes['is_remote'] = $is;

        return $this;
    }

    public function storage(string $storageType = 'image')
    {
        $this->attributes['storage_type'] = $storageType;

        return $this;
    }

    public function imgHeight(string $height = '50px')
    {
        $this->attributes['img_height'] = $height;

        return $this;
    }
}
