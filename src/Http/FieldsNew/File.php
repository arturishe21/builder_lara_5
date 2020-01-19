<?php

namespace Vis\Builder\FieldsNew;

use Illuminate\Support\Str;

class File extends Field
{
    protected $accept;
    protected $path = '/storage/files/';

    public function getAccept()
    {
        return $this->accept;
    }

    public function getValueForList($definition)
    {
        if ($this->getValue()) {
            return "<a href='{$this->getValue()}' target='_blank'>" . __cms('Скачать') . "</a>";
        }
    }

    public function accept($value)
    {
        $this->accept = 'accept="'. $value .'"';

        return $this;
    }

    public function upload()
    {
        $file = request()->file('file');

        $extension = $file->getClientOriginalExtension();
        $nameFileArray = explode('.', $file->getClientOriginalName());
        $nameFile = Str::slug($nameFileArray[0]);

        $fileName = $nameFile . '.' . $extension;

        if (file_exists(public_path($this->path . $fileName))) {
            $fileName = $nameFile . '_' . time() . '.' . $extension;
        }

        $file->move(ltrim($this->path, '/'), $fileName);

        return [
            'status'     => true,
            'link'       => asset($this->path . $fileName),
            'short_link' => $fileName,
            'long_link'  => $this->path . $fileName,
        ];
    }
}
