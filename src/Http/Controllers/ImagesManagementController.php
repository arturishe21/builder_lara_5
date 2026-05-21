<?php

namespace Vis\Builder\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Vis\Builder\Http\Definitions\Resource;
use Vis\Builder\Http\Fields\Field;
use Vis\Builder\Http\Requests\UploadPictureRequest;

class ImagesManagementController extends Controller
{
    private ?Resource $definition;

    public function __construct()
    {
        $pathDefinition = request('path_model');
        $this->definition = $pathDefinition ? new $pathDefinition() : null;
    }

    public function upload(UploadPictureRequest $request)
    {
        return $this->getThisField()->upload($this->definition, $request->file('image'));
    }

    public function selectPhotos(): JsonResponse
    {
        return $this->getThisField()->selectWithUploadedImages();
    }

    private function getThisField(): Field
    {
        return $this->definition->getAllFields()[request('ident')];
    }
}

