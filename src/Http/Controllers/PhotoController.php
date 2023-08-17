<?php

namespace Vis\Builder\Http\Controllers;

use Illuminate\Routing\Controller;

class PhotoController extends Controller
{
    private $definition;

    public function __construct()
    {
        $pathDefinition = request('path_model');

        $this->definition = new $pathDefinition();
    }

    public function upload()
    {
        return $this->getThisField()->upload($this->definition);
    }

    public function selectPhotos()
    {
        return $this->getThisField()->selectWithUploadedImages($this->definition);
    }

    private function getThisField()
    {
        return $this->definition->getAllFields()[request('ident')];
    }
}

