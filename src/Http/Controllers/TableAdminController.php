<?php

namespace Vis\Builder;

use Vis\Builder\Services\Actions;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

/**
 * Class TableAdminController.
 */
class TableAdminController extends Controller
{
    /**
     * @return mixed
     */
    /**
     * @param string $page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPage($page)
    {
        $modelDefinition = $this->getModelDefinition($page);

        $this->checkExistsClass($modelDefinition);

        $model = new $modelDefinition();
        $data = $model->getList();

        return view($model->getTableView(), compact('data'));
    }

    /**
     * @param $page
     *
     * @return mixed
     */
    public function showPagePost($page)
    {
        $modelDefinition = $this->getModelDefinition($page);

        $this->checkExistsClass($modelDefinition);

        return (new $modelDefinition())->getList();
    }

    /**
     * @param $page
     *
     * @return mixed
     */
    public function actionsPage($page)
    {
        $modelDefinition = $this->getModelDefinition($page);

        return (new Actions(new $modelDefinition()))->router(request('query_type'));
    }

    public function fastEdit($page, $id)
    {
        $modelDefinition = $this->getModelDefinition($page);

        request()->merge(['ident' => request('name')]);

        return (new Actions(new $modelDefinition()))->router('do_fast_change_field');
    }

    private function getModelDefinition($page)
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

    private function checkExistsClass($modelDefinition)
    {
        if (!class_exists($modelDefinition)) {
            throw new \Exception('Not found class '. $modelDefinition);
        }
    }

}
