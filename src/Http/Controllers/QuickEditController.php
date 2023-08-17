<?php

namespace Vis\Builder\Http\Controllers;

use Illuminate\Routing\Controller;

class QuickEditController extends Controller
{
    public function __invoke(): void
    {
        $model = request('model');
        $id = request('id');
        $field = request('field');
        $text = request('text');

        $page = $model::find($id);
        $page->$field = $text;
        $page->save();
    }
}

