<?php

namespace Vis\Builder;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Vis\Builder\Http\Requests\EditorFile;
use Vis\Builder\Http\Requests\EditorImage;
use Illuminate\Support\Facades\File;

class EditorController extends Controller
{
    private $pathPhotos = '/storage/editor/photos';
    private $pathFiles = '/storage/editor/files';

    public function uploadImage(EditorImage $request)
    {
        return $this->uploadFileAndReturnResult($request, $this->pathPhotos);
    }

    public function uploadFile(EditorFile $request)
    {
        return $this->uploadFileAndReturnResult($request, $this->pathFiles);
    }

    private function uploadFileAndReturnResult($request, $path)
    {
        $nameFile = $this->getNameFile($request->file('file'), $path);

        $request->file('file')->move(public_path($path), $nameFile);

        return response()->json([
            'link' => $path. '/' . $nameFile
        ]);
    }

    private function getNameFile($file, $path)
    {
        if (!file_exists(public_path($path))) {
            mkdir(public_path($path), 0755, true);
        }

        $ext = $file->getClientOriginalExtension();
        $fullname = $file->getClientOriginalName();
        $fullname = str_replace('.'.$ext, '', $fullname);

        $resultName = Str::slug($fullname) . '.' . $ext;
        $fullPathImg = $path . '/' . $resultName;

        if (file_exists(public_path($fullPathImg))) {
            $resultName = Str::slug($fullname).'_'.time().'.'.$ext;
        }

        return $resultName;
    }

    public function getUploadedImages()
    {
        $files = File::files(public_path($this->pathPhotos));

        $result = array_map(function ($file) {
            return [
                'url' => $this->pathPhotos. '/'. $file->getFilename(),
                'thumb' => $this->pathPhotos. '/'. $file->getFilename()
            ];

        }, $files);

        return response()->json($result);
    }

    public function deleteImage()
    {
        unlink(public_path(request('src')));
    }
}
