<?php

namespace Vis\Builder;

use Symfony\Component\ErrorHandler\Error\ClassNotFoundError;
use Vis\Builder\Services\Actions;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Vis\Builder\ControllersNew\{TreeController};

/**
 * Class TableAdminController.
 */
class TableAdminController extends Controller
{
    /**
     * @return mixed
     */
    public function showTreeNew()
    {
        $modelDefinition = "App\\Cms\\Tree\\" . Str::title('tree');

        return (new TreeController($modelDefinition))->list();
    }

    public function handleTreeNew()
    {
        $modelDefinition = "App\\Cms\\Tree\\" . Str::title('tree');

        return (new TreeController($modelDefinition))->handle();
    }

    /**
     * @param string $page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showPage($page)
    {
        $modelDefinition = $this->getModelDefinition($page);

        if (class_exists($modelDefinition)) {

            $model = new $modelDefinition();

            $data = $model->getList();

            return view($model->getTableView(), compact('data'));
        }

        throw new \Exception('Not found class '. $modelDefinition);
    }

    /**
     * @param $page
     *
     * @return mixed
     */
    public function showPagePost($page)
    {
        $modelDefinition = $this->getModelDefinition($page);

        if (class_exists($modelDefinition)) {
            return (new $modelDefinition())->getList();
        }

        throw new \Exception('Not found class '. $modelDefinition);
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

        return "App\\Cms\\Definitions\\" . ucfirst(Str::camel($page));
    }

    public function showTreeAll($page)
    {
        $modelPath = "App\\Models\\" . ucfirst(Str::camel($page));

        $model = new $modelPath();
        $tree = $model::with('children')->defaultOrder()->get()->toTree();
        $parentIDs = [];

        return view('admin::tree.tree', compact('tree', 'parentIDs'));
    }


}
