<?php

namespace Vis\Builder\ControllersNew;

use Illuminate\Support\Facades\Cache;

class EditContentOnSiteController
{
    public function index()
    {
        $model = request('model')::find(request('id'));

        $data = json_decode($model->{request('field')}, true);
        $data[request('language')] = request('value');
        $model->{request('field')} = json_encode($data);
        $model->save();

        Cache::flush();
    }
}
