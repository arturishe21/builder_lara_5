<?php

namespace Vis\Builder\Http\Controllers;

use Illuminate\Routing\Controller;
use Vis\Builder\Http\Definitions\Resource;
use Vis\Builder\Http\Requests\UploadFileRequest;
use Illuminate\Http\JsonResponse;
use Vis\Builder\Http\Fields\File;

class FilesManagementController extends Controller
{
    private Resource $definition;

    public function __construct()
    {
        $pathDefinition = request('path_model');

        $this->definition = new $pathDefinition();
    }

    public function upload(UploadFileRequest $request): JsonResponse
    {
        return $this->getThisField()->upload($request->file('file'));
    }

    public function selectFiles(): JsonResponse
    {
        return $this->getThisField()->selectWithUploadedFiles();
    }

    private function getThisField(): File
    {
        return $this->definition->getAllFields()[request('ident')];
    }
}

