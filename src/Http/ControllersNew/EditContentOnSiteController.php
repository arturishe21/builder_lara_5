<?php

namespace Vis\Builder\ControllersNew;

use Illuminate\Support\Facades\Cache;

class EditContentOnSiteController
{
    public function index()
    {
        $model = request('model')::find(request('id'));
        $model->{request('field')} = request('value');
        $model->save();

        Cache::flush();
    }
}
