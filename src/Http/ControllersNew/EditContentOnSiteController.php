<?php

namespace Vis\Builder\ControllersNew;

class EditContentOnSiteController
{
    public function index()
    {
        $model = request('model')::find(request('id'));
        $model->{request('field')} = request('value');
        $model->save();
    }
}
