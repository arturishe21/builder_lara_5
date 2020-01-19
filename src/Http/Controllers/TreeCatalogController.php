<?php

namespace Vis\Builder;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class TreeCatalogController
{
    protected $model;
    protected $options;
    protected $nameTree;
    protected $controller;

    public function __construct($model, array $options, $nameTree)
    {
        $this->model = $model;
        $this->options = $options;
        $this->nameTree = $nameTree;

        $this->controller = new JarboeController($options);
    }

    public function setOptions(array $options = [])
    {
        $this->options = $options;
    }

    public function handle()
    {
        switch (request('query_type')) {
            case 'do_create_node':
                return $this->doCreateNode();

            case 'clone_record_tree':
                return $this->doCloneRecord();

            case 'do_change_active_status':
                return $this->doChangeActiveStatus();

            case 'do_change_position':
                return $this->doChangePosition();

            case 'do_delete_node':
                return $this->doDeleteNode();

            case 'do_edit_node':
                return $this->doEditNode();

            case 'do_update_node':
                return $this->doUpdateNode();

            case 'get_edit_modal_form':
                return $this->getEditModalForm();

            case 'fast_save':
                return $this->doFastSave();

            default:
                return $this->handleShowCatalog();
        }
    }

    public function doUpdateNode()
    {
        $node = $this->model::find(request('pk'));
        $node->template = request('value');
        $node->save();

        $node->clearCache();
    }

    public function doCreateNode()
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

        return response()->json([
            'status' => true,
        ]);
    }

    public function doCloneRecord()
    {
        $model = $this->model;
        $root = $model::find(request('node', 1));
        $id = request('id');

        $this->cloneRecursively($id);

        $root->clearCache();

        return [
            'id' => $id,
        ];
    }

    private function cloneRecursively($id, $parentId = '')
    {
        $model = $this->model;

        $page = $model::where('id', $id)->select('*')->first()->toArray();
        $idClonePage = $page['id'];
        unset($page['id']);
        if ($parentId) {
            $page['parent_id'] = $parentId;
        }

        if ($page['parent_id']) {
            $root = $model::find($page['parent_id']);

            $rec = new $model();
            $countPages = $model::where('parent_id', $page['parent_id'])->where('slug', $page['slug'])->count();

            if ($countPages) {
                $page['slug'] = $parentId ?
                                        $page['slug'].'_'.$page['parent_id'] :
                                        $page['slug'].'_'.time();
            }

            foreach ($page as $k => $val) {
                $rec->$k = $val;
            }

            $rec->save();
            $lastId = $rec->id;

            $rec->makeChildOf($root);
        }

        $folderCheck = $model::where('parent_id', $idClonePage)->select('*')->orderBy('lft', 'desc')->get()->toArray();
        if (count($folderCheck)) {
            foreach ($folderCheck as $pageChild) {
                $this->cloneRecursively($pageChild['id'], $lastId);
            }
        }
    }

    public function doChangeActiveStatus()
    {
        $node = $this->model::find(request('id'));
        $node->is_active = request('is_active') ? '1' : '0';

        $node->save();

        $node->clearCache();

        return response()->json([
            'active' => true,
        ]);
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
                $item->moveToRightOf($this->model::find($idLeftSibling));
            } elseif ($idRightSibling) {
                $item->moveToLeftOf($this->model::find($idRightSibling));
            }
        }

        $root->clearCache();

        $item = $this->model::find($item->id);
        $item->checkUnicUrl();

        $data = [
            'status'    => true,
            'item'      => $item,
            'parent_id' => $root->id,
        ];

        return response()->json($data);
    }

    public function process()
    {
        $idNode = request('page_id', request('node', 1));
        $current = $this->model::find($idNode);
        $template = $this->getTemplate($current);

        return \Jarboe::table([
            'url'        => url()->current(),
            'def_name'   => $this->nameTree.'.'.$template['node_definition'],
            'additional' => [
                'node'    => $idNode,
                'current' => $current,
            ],
        ]);
    }

    public function doDeleteNode()
    {
        $this->model::destroy(request('id'));

        $this->model::root()->clearCache();

        return response()->json([
            'status' => true,
        ]);
    }

    private function handleShowCatalog()
    {
        $treeName = $this->nameTree;

        $controller = $this->controller;
        $perPage = Session::get('table_builder.'.$treeName.'.node.per_page', 20);
        $current = $this->model::findOrFail(request('node', 1));

        $children = $current->children();

        //filter children by action check which can return array of id
        $actions = config('builder.'.$treeName.'.actions.show');

        if ($actions && $actions['check']() !== true && is_array($actions['check']())) {
            $arrIdsShow = $actions['check']();

            foreach ($actions['check']() as $id) {
                $arrIdsShow[] = $this->model::find($id)->getDescendants()->pluck('id')->toArray();
            }

            $arrIdsShow = array_flatten($arrIdsShow);
            $children = $children->whereIn('id', $arrIdsShow);
        }

        $children = $children->withCount('children')->paginate($perPage);

        $buttons = isset($this->controller->buttons) ? $this->controller->buttons->fetch() : '';

        $content = view('admin::tree.content',
            compact('current', 'treeName', 'children', 'controller', 'perPage'));
        $treeView = request()->ajax() ? 'tree_ajax' : 'tree';

        return view('admin::'.$treeView,
            compact('content', 'current', 'treeName', 'controller', 'perPage', 'buttons'));
    }

    public function getEditModalForm()
    {
        $nodeId = request('id');
        $current = $this->model::findOrFail($nodeId);

        $template = $this->getTemplate($current);

        $jarboeController = new JarboeController([
            'url'        => url()->current(),
            'def_name'   => $this->nameTree.'.'.$template['node_definition'],
            'additional' => [
                'node'    => $nodeId,
                'current' => $current,
            ],
        ]);

        $html = $jarboeController->view->showEditForm($nodeId, true);

        return response()->json([
            'status' => true,
            'html'   => $html,
        ]);
    }

    public function doEditNode()
    {
        $idNode = request('id');
        $current = $this->model::find($idNode);

        $template = $this->getTemplate($current);

        $controller = new JarboeController([
            'url'        => url()->current(),
            'def_name'   => $this->nameTree.'.'.$template['node_definition'],
            'additional' => [
                'node'    => $idNode,
                'current' => $current,
            ],
        ]);

        $result = $controller->query->updateRow(request()->all());

        $item = $this->model::find($idNode);
        $item->clearCache();
        $item->checkUnicUrl();
        $treeName = $this->nameTree;
        $result['html'] = view('admin::tree.content_row',
            compact('item', 'treeName', 'controller'))->render();

        return response()->json($result);
    }

    private function getTemplate($current)
    {
        $templates = config('builder.'.$this->nameTree.'.templates');
        $template = config('builder.'.$this->nameTree.'.default');

        if (isset($templates[$current->template])) {
            $template = $templates[$current->template];
        }

        return $template;
    }

    public function doFastSave()
    {
        $field = request('name');

        $fieldArray = request($field) ?? [];

        $data['value'] = json_encode(array_values($fieldArray));
        $data['name'] = $field;
        $data['id'] = request('id');

        $result = $this->controller->query->fastSave($data);
        $result['status'] = 'ok';

        return response()->json($result);
    }
}
