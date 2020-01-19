<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Vis\Builder\Facades\Jarboe;

class ImageField extends AbstractField
{
    public function isEditable()
    {
        return true;
    }

    // end isEditable

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

    // end getListValue

    private function getListSingle($row)
    {
        $pathPhoto = $this->getValue($row);

        if (! $pathPhoto) {
            return '';
        }

        $html = '<a class="screenshot"  rel="'.glide($pathPhoto, ['w' => '350']).'">
                    <img src="'.glide($pathPhoto, ['w' => '50']).'" /></a>';

        return $html;
    }

    // end getListSingle

    private function getListMultiple($row)
    {
        if (! $this->getValue($row)) {
            return '';
        }

        $images = json_decode($this->getValue($row), true);

        $html = '<div style="cursor:pointer;height: 50px;overflow: hidden;" onclick="$(this).css(\'height\', \'auto\').css(\'overflow\', \'auto\');">';
        foreach ($images as $source) {
            $src = $source;

            $src = $this->getAttribute('is_remote') ? $src : URL::asset($src);
            $html .= '<img height="'.$this->getAttribute('img_height', '50px').'" src="'
                .$src
                .'" /><br>';
        }

        $html .= '</div>';

        return $html;
    }

    // end getListMultiple

    public function onSearchFilter(&$db, $value)
    {
        $db->where($this->getFieldName(), 'LIKE', '%'.$value.'%');
    }

    // end onSearchFilter

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

    // end getTabbedEditInput

    protected function getPreparedTabs($row)
    {
        $tabs = $this->getAttribute('tabs');

        foreach ($tabs as &$tab) {
            $tab['value'] = $this->getValue($row, $tab['postfix']);
        }

        return $tabs;
    }

    // end getPreparedTabs

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

    // end getEditInput

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
            App::abort(500, 'Ошибка загрузки файла. Файл больше чем '.$this->getAttribute('limit_mb').' МБ');
        }
    }

    public function prepareQueryValue($value)
    {
        if (! $value) {
            return '';
        }

        return $value;
    }
}
