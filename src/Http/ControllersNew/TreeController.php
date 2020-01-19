<?php

namespace Vis\Builder\ControllersNew;

use Illuminate\Support\Str;

class TreeController
{
    protected $definition;
    protected $model;

    public function __construct($definition)
    {
        $this->definition = new $definition;
        $this->model = $this->definition->model();
    }

    public function list()
    {
        $treeName = 'tree';

        if (request('query_type')) {
            $method = Str::camel(request('query_type'));

            return $this->$method(request()->except('query_type'));
        }

        $current = $this->model::findOrFail(request('node', 1));
        $perPage = 20;
        $children = $current->children();
        $children = $children->withCount('children')->paginate($perPage);
        $templates = $this->definition->getTemplates();
        $definition = $this->definition;

        $content = view('admin::new.tree.content',
            compact('current', 'treeName', 'children', 'controller', 'perPage', 'templates', 'definition'));

        $view = request()->ajax() ? 'center' : 'table';

        return view('admin::new.tree.' . $view,
            compact( 'treeName', 'current', 'children', 'content', 'definition', 'templates'));
    }

    public function handle()
    {
        $definitionModel = $this->getDefinitionModel(request()->all());

        return (new $definitionModel())->saveEditForm(request()->all());
    }

    public function doChangeActiveStatus($request)
    {
        $tree = $this->model::find($request['id']);
        $tree->is_active = $request['is_active'];
        $tree->save();
    }

    public function doDeleteNode($request)
    {
        $this->definition->model()->destroy($request['id']);

        return [
            'status' => 'success'
        ];
    }

    public function doChangePosition($request)
    {
        $id = request('id');
        $idParent = request('parent_id', 1);
        $idLeftSibling = request('left_sibling_id');
        $idRightSibling = request('right_sibling_id');

        $item = $this->model::find($id);
        $root = $this->model::find($idParent);

        $prevParentID = $item->parent_id;
        $item->makeChildOf($root);

        $item->save();

        if ($prevParentID == $idParent) {
            if ($idLeftSibling) {
                $item->moveToRightOf($this->model::find($idLeftSibling));
            } elseif ($idRightSibling) {
                $item->moveToLeftOf($this->model::find($idRightSibling));
            }
        }

        $root->clearCache();

        $item = $this->model::find($item->id);
        $item->checkUnicUrl();

        return [
            'status'    => true,
            'item'      => $item,
            'parent_id' => $root->id,
        ];
    }

    private function getDefinitionModel($request)
    {
        $model = $this->model::find($request['id']);

        return $this->definition->templates()[$model->template];
    }

    private function getEditModalForm($request)
    {
        $definitionModel = $this->getDefinitionModel($request);

        return (new $definitionModel())->showEditForm($request['id']);
    }

    private function doChangeTemplate($request)
    {
        $tree = $this->model::find($request['pk']);
        $tree->template = $request['value'];
        $tree->save();
    }

    private function doCreateNode($request)
    {
        $model = $this->model;

        $root = $model::find(request('node', 1));

        $node = new $model();

        $node->parent_id = request('node', 1);
        $node->title = request('title');
        $node->template = request('template') ?: '';
        $node->slug = Str::slug(request('title'));

        $node->save();

        $node->checkUnicUrl();

        $root->children()->count() == 1 ? $node->makeChildOf($root) : $node->makeFirstChildOf($root);

        $root->clearCache();

        return [
            'status' => true,
        ];
    }
}
