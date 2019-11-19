<?php

namespace Vis\Builder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

/**
 * Class TBTreeController.
 */
class TBTreeController extends \Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showTree()
    {
        $tree = Tree::all()->toHierarchy();

        $idNode = request('node', 1);
        $current = Tree::find($idNode);

        $parentIDs = [];
        foreach ($current->getAncestors() as $anc) {
            $parentIDs[] = $anc->id;
        }

        $templates = config('builder::tree.templates');
        $template = config('builder::tree.default');
        if (isset($templates[$current->template])) {
            $template = $templates[$current->template];
        }

        if ($template['type'] == 'table') {
            $options = [
                'url'        => \URL::current(),
                'def_name'   => 'tree.'.$template['definition'],
                'additional' => [
                    'node' => $idNode,
                ],
            ];
            [$table, $form] = \Jarboe::table($options);
            $content = view('admin::tree.content', compact('current', 'table', 'form', 'template'));
        } else {
            $content = view('admin::tree.content', compact('current', 'template'));
        }

        return view('admin::tree', compact('tree', 'content', 'current', 'parentIDs'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEditModalForm()
    {
        $idNode = request('id');
        $current = Tree::find($idNode);
        $templates = config('builder::tree.templates');
        $template = config('builder::tree.default');
        if (isset($templates[$current->template])) {
            $template = $templates[$current->template];
        }

        $options = [
            'url'        => \URL::current(),
            'def_name'   => 'tree.'.$template['node_definition'],
            'additional' => [
                'node' => $idNode,
            ],
        ];
        $controller = new JarboeController($options);

        $html = $controller->view->showEditForm($idNode, true);

        return Response::json([
            'status' => true,
            'html'   => $html,
        ]);
    }

    /**
     * @throws \Throwable
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function doEditNode()
    {
        $idNode = request('id');
        $current = Tree::find($idNode);
        $templates = config('builder::tree.templates');
        $template = config('builder::tree.default');

        if (isset($templates[$current->template])) {
            $template = $templates[$current->template];
        }

        $options = [
            'url'        => \URL::current(),
            'def_name'   => 'tree.'.$template['definition'],
            'additional' => [
                'node' => $idNode,
            ],
        ];

        $controller = new JarboeController($options);

        $result = $controller->query->updateRow(request()->all());
        $item = Tree::find($idNode);
        $result['html'] = view('admin::tree.content_row', compact('item'))->render();

        $this->doFlushTreeStructureCache();

        return Response::json($result);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function doDeleteNode()
    {
        $item = Tree::find(request('id'));
        $status = $item->delete();

        $this->doFlushTreeStructureCache();

        return Response::json([
            'status' => $status,
        ]);
    }

    /**
     * @return mixed
     */
    public function handleTree()
    {
        $idNode = request('node', 1);
        $current = Tree::find($idNode);

        $templates = config('builder::tree.templates');
        $template = config('builder::tree.default');
        if (isset($templates[$current->template])) {
            $template = $templates[$current->template];
        }

        if ($template['type'] == 'table') {
            $options = [
                'url'        => \URL::current(),
                'def_name'   => 'tree.'.$template['definition'],
                'additional' => [
                    'node' => $idNode,
                ],
            ];

            return \Jarboe::table($options);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePosition()
    {
        $id = request('id');
        $idParent = request('parent_id', 1);
        $idLeftSibling = request('left_sibling_id');
        $idRightSibling = request('right_sibling_id');

        $item = Tree::find($id);
        $root = Tree::find($idParent);

        $prevParentID = $item->parent_id;
        $item->makeChildOf($root);

        $item->save();

        if ($prevParentID == $idParent) {
            if ($idLeftSibling) {
                $item->moveToRightOf(Tree::find($idLeftSibling));
            } elseif ($idRightSibling) {
                $item->moveToLeftOf(Tree::find($idRightSibling));
            }
        }

        $this->doFlushTreeStructureCache();

        $item = Tree::find($item->id);

        $data = [
            'status'    => true,
            'item'      => $item,
            'parent_id' => $root->id,
        ];

        return Response::json($data);
    }

    private function doFlushTreeStructureCache()
    {
        \Cache::tags('j_tree')->flush();
    }

    public function changeActive()
    {
        $activeField = config('builder::tree.node_active_field.field');
        $options = config('builder::tree.node_active_field.options', []);

        $value = request('is_active');
        if ($options) {
            $value = implode(array_filter(request('onoffswitch', [])), ',');
        }

        DB::table('tb_tree')->where('id', request('id'))->update([
            $activeField => $value,
        ]);

        $this->doFlushTreeStructureCache();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function doCreateNode()
    {
        $root = Tree::find(request('node', 1));

        $node = new Tree();
        $node->parent_id = request('node', 1);
        $node->title = request('title');
        $node->slug = request('slug') ?: request('title');
        $node->template = request('template');
        $node->is_active = '0';
        $node->save();

        $node->makeChildOf($root);

        $this->doFlushTreeStructureCache();

        return Response::json([
            'status' => true,
        ]);
    }
}
