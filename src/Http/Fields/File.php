<?php

namespace Vis\Builder\Http\Fields;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class File extends Field
{
    protected string $accept = '';
    protected string $path = '/storage/files/';
    protected $isAutoTranslate = false;
    protected bool $selectWithUploaded = true;

    public function getAccept(): string
    {
        return $this->accept;
    }

    public function getValueForList($definition)
    {
        $value = $this->getValue();

        if ($this->getLanguage()) {
            $language = $this->getLanguage()->first();
            $value = $this->getValueLanguage($language->language);
        }

        if ($value) {
            return "<a href='{$value}' target='_blank'>" . __cms('Скачать') . "</a>";
        }
    }

    public function accept(string $value): self
    {
        $this->accept = 'accept="'. $value .'"';

        return $this;
    }

    public function noFileSelection(): self
    {
        $this->selectWithUploaded = false;

        return $this;
    }

    public function checkSelectionFiles(): bool
    {
        return $this->selectWithUploaded;
    }

    public function uploadPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function upload(UploadedFile $file): JsonResponse
    {
        if (!file_exists(public_path($this->path))) {
            mkdir(public_path($this->path), 0755, true);
        }

        $extension = $file->getClientOriginalExtension();
        $nameFileArray = explode('.', $file->getClientOriginalName());
        $nameFile = Str::slug($nameFileArray[0]);

        $fileName = $nameFile . '.' . $extension;

        if (file_exists(public_path($this->path . $fileName))) {
            $fileName = $nameFile . '_' . time() . '.' . $extension;
        }

        $file->move(public_path($this->path), $fileName);

        return response()->json([
            'status'     => true,
            'link'       => asset($this->path . $fileName),
            'short_link' => $fileName,
            'long_link'  => $this->path . $fileName,
        ]);
    }

    public function selectWithUploadedFiles(): JsonResponse
    {
        $files = collect(FileFacade::files(public_path($this->path)))->sortBy(function ($file) {
            return filemtime($file);
        })->reverse();

        $page = (int) request('page') ?: 1;
        $onPage = 24;
        $slice = $files->slice(($page - 1) * $onPage, $onPage);

        $list = new LengthAwarePaginator($slice, $files->count(), $onPage);
        $list->setPath(url()->current());

        return response()->json([
            'status' => 'success',
            'data'   => view('admin::form.fields.partials.files_list', compact('list'))->render(),
        ]);
    }
}
