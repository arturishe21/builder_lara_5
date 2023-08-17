<?php

namespace Vis\Builder\Http\Controllers;

use Vis\Builder\Http\Services\Actions;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;

class TableAdminController extends Controller
{
    public function showPage(string $page): View
    {
        $modelDefinition = $this->getModelDefinition($page);

        $this->checkExistsClass($modelDefinition);

        $model = new $modelDefinition();
        $data = $model->getList();

        return view($model->getTableView(), compact('data'));
    }

    public function showPagePost(string $page)
    {
        $modelDefinition = $this->getModelDefinition($page);

        $this->checkExistsClass($modelDefinition);

        return (new $modelDefinition())->getList();
    }

    public function actionsPage(string $page)
    {
        $modelDefinition = $this->getModelDefinition($page);

        return (new Actions(new $modelDefinition()))->router(request('query_type'));
    }

    public function fastEdit(string $page, $id)
    {
        $modelDefinition = $this->getModelDefinition($page);

        request()->merge(['ident' => request('name')]);

        return (new Actions(new $modelDefinition()))->router('do_fast_change_field');
    }

    private function getModelDefinition(string $page): string
    {
        if (request('foreign_attributes')) {
            $arrayAttributes = json_decode(request('foreign_attributes'), 'true');

            return $arrayAttributes['path_definition'];
        }

        if (request('paramsJson')) {
            $arrayAttributes = json_decode(request('paramsJson'), 'true');

            return $arrayAttributes['model_parent'];
        }

        if (request('model_definitions')) {
            return request('model_definitions');
        }

        return "App\\Cms\\Definitions\\" . ucfirst(Str::camel($page));
    }

    private function checkExistsClass(string $modelDefinition)
    {
        if (!class_exists($modelDefinition)) {
            throw new \Exception('Not found class '. $modelDefinition);
        }
    }

}
