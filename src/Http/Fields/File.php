<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File as FileFacade;

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

    public function selectWithUploadedFiles($definition)
    {
        return $this->getFilesDefaultPath();
    }

    private function getFilesDefaultPath()
    {
        $files = collect(FileFacade::files(public_path($this->path)))->sortBy(function ($file) {
            return filemtime($file);
        })->reverse();

        $page = (int) request('page') ?: 1;
        $onPage = 24;
        $slice = $files->slice(($page - 1) * $onPage, $onPage);

        $list = new \Illuminate\Pagination\LengthAwarePaginator($slice, $files->count(), $onPage);
        $list->setPath(url()->current());

        return [
            'status' => 'success',
            'data'   => view('admin::new.form.fields.partials.files_list', compact('list'))->render(),
        ];
    }
}
