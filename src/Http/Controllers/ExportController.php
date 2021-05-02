<?php

namespace Vis\Builder;

use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Vis\Builder\Services\Listing;

class ExportController extends Controller
{
    public function download($definition)
    {
       $modelDefinition = $this->getModelDefinition($definition);
       $modelExport = request('model');

       $listing = new Listing(new $modelDefinition());

       return (new $modelExport($listing))->download($definition . '_' . Carbon::now() . '.xlsx');
    }

    private function getModelDefinition($definition)
    {
        return "App\\Cms\\Definitions\\" . ucfirst(Str::camel($definition));
    }
}

