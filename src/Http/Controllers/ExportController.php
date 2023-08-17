<?php

namespace Vis\Builder\Http\Controllers;

use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Vis\Builder\Http\Services\Listing;

class ExportController extends Controller
{
    public function download(string $definition)
    {
       $modelDefinition = $this->getModelDefinition($definition);
       $modelExport = request('model');

       $listing = new Listing(new $modelDefinition());

       return (new $modelExport($listing))->download($definition . '_' . Carbon::now() . '.xlsx');
    }

    private function getModelDefinition(string $definition): string
    {
        return "App\\Cms\\Definitions\\" . ucfirst(Str::camel($definition));
    }
}

