<?php

namespace Vis\Builder\ControllersNew;

use Illuminate\Support\Str;
use Vis\Builder\Services\Revisions;
use Vis\Builder\Libs\GoogleTranslateForFree;

class TreeController
{
    protected $definition;
    protected $model;
    protected $revision;

    public function __construct($definition)
    {
        $this->definition = $definition;
        $this->model = $this->definition->model();
        $this->revision = new Revisions();
    }

    public function list()
    {
        $this->checkPermissions();

        $treeName = 'tree';

        if (request('query_type')) {
            $method = Str::camel(request('query_type'));

            return $this->$method(request()->except('query_type'));
        }

        $current = $this->model::findOrFail(request('node', 1));
        $perPage = 20;
        $children = $current->children();

        $children = $children->withCount('children')->defaultOrder()->paginate($perPage);
        $templates = $this->definition->getTemplates();
        $definition = $this->definition;

        $content = view('admin::tree.content',
            compact('current', 'treeName', 'children', 'perPage', 'templates', 'definition'));

        $view = request()->ajax() ? 'center' : 'table';

        return view('admin::tree.' . $view,
            compact( 'treeName', 'current', 'children', 'content', 'definition', 'templates'));
    }

    public function handle()
    {

        if (in_array(request('query_type'),
            ['delete_foreign_row', 'get_html_foreign_definition', 'show_revisions', 'return_revisions'])) {
            $method = Str::camel(request('query_type'));

            return $this->$method(request()->except('query_type'));
        }

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

        $this->definition->clearCache();

        return [
            'status' => 'success'
        ];
    }

    public function doChangePosition()
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
                $item->insertAfterNode($this->model::find($idLeftSibling));
            } elseif ($idRightSibling) {
                $item->insertBeforeNode($this->model::find($idRightSibling));
            }
        }

        $item = $this->model::find($item->id);
        $item->checkUnicUrl();

        $this->definition->clearCache();

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

    private function cloneRecordTree($request)
    {
        $definitionModel = $this->getDefinitionModel($request);

        return (new $definitionModel())->cloneTree($request['id']);
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

        $languages = languagesOfSite();

        foreach ($languages as $language) {
            $translations[$language] =
                (new GoogleTranslateForFree())->translate(
                    defaultLanguage(),
                    $language,
                    request('title'),
                    1);
        }

        $node->title = json_encode($translations);
        $node->template = request('template') ?: '';
        $node->slug = Str::slug(request('title'));

        $node->save();
        $node->checkUnicUrl();
        $node->prependToNode($root)->save();

        $root->clearCache();

        return [
            'status' => true,
        ];
    }

    private function getHtmlForeignDefinition($request)
    {
        $definition = resolve($this->getDefinitionModel($request));

        $parseJsonData = (array) json_decode($request['paramsJson']);
        $field = $definition->getAllFields()[$parseJsonData['ident']];

        return $field->getTable($definition, $parseJsonData);
    }

    private function deleteForeignRow($request)
    {
        $definition = resolve($this->getDefinitionModel($request));

        $parseJsonData = (array) json_decode($request['paramsJson']);
        $field = $definition->getAllFields()[$parseJsonData['ident']];

        return $field->remove($definition, $parseJsonData);
    }

    private function showRevisions($request)
    {
        $definition = resolve($this->getDefinitionModel($request));

        return $this->revision->show($request['id'], $definition);
    }

    private function returnRevisions($request)
    {
        return $this->revision->doReturn($request['id']);
    }

    private function doFastChangeField()
    {
        $tree = $this->model::find(request('pk'));
        $tree->is_active = request('value');
        $tree->save();

        $tree->clearCache();
    }

    protected function checkPermissions()
    {
        if (!app('user')->hasAccess(['tree.view'])) {
            abort(403);
        }
    }
}
