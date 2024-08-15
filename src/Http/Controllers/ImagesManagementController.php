<?php

namespace Vis\Builder\Http\Controllers;

use Illuminate\Routing\Controller;
use Vis\Builder\Http\Definitions\Resource;
use Vis\Builder\Http\Requests\UploadPictureRequest;

class ImagesManagementController extends Controller
{
    private Resource $definition;

    public function __construct()
    {
        $pathDefinition = request('path_model');

        $this->definition = new $pathDefinition();
    }

    public function upload(UploadPictureRequest $request)
    {
        return $this->getThisField()->upload($this->definition, $request->file('image'));
    }

    public function selectPhotos()
    {
        return $this->getThisField()->selectWithUploadedImages();
    }

    private function getThisField()
    {
        return $this->definition->getAllFields()[request('ident')];
    }
}

