<?php

namespace Vis\Builder\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Vis\Builder\Http\Requests\EditorFileRequest;
use Vis\Builder\Http\Requests\EditorImageRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;

class EditorController extends Controller
{
    private string $pathPhotos = '/storage/editor/photos';
    private string $pathFiles = '/storage/editor/files';

    public function uploadImage(EditorImageRequest $request): JsonResponse
    {
        return $this->uploadFileAndReturnResult($request->file('file'), $this->pathPhotos);
    }

    public function uploadFile(EditorFileRequest $request): JsonResponse
    {
        return $this->uploadFileAndReturnResult($request->file('file'), $this->pathFiles);
    }

    private function uploadFileAndReturnResult(UploadedFile $file, string $path): JsonResponse
    {
        $nameFile = $this->getNameFile($file, $path);

        $file->move(public_path($path), $nameFile);

        return response()->json([
            'link' => $path. '/' . $nameFile
        ]);
    }

    private function getNameFile(UploadedFile $file, string $path): string
    {
        if (!file_exists(public_path($path))) {
            mkdir(public_path($path), 0755, true);
        }

        $ext = $file->getClientOriginalExtension();
        $fullname = str_replace('.'.$ext, '', $file->getClientOriginalName());

        $resultName = Str::slug($fullname) . '.' . $ext;
        $fullPathImg = $path . '/' . $resultName;

        if (file_exists(public_path($fullPathImg))) {
            $resultName = Str::slug($fullname).'_'.time().'.'.$ext;
        }

        return $resultName;
    }

    public function getUploadedImages(): JsonResponse
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
}
