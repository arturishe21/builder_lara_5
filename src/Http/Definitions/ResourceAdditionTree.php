<?php

namespace Vis\Builder\Http\Definitions;

use Vis\Builder\Http\ControllersNew\TreeController;
use Vis\Builder\Http\Services\Listing;
use Illuminate\View\View;

class ResourceAdditionTree extends Resource
{
    public function getTableView(): string
    {
        return 'admin::addition_tree.table';
    }

    public function getList(): View
    {
        $list = new Listing($this);
        $listingRecords = $list->body();
        $current = $this->model()->findOrFail(request('node', 1));
        $definition = $this;

        return view('admin::addition_tree.list.table',
            compact('list', 'listingRecords', 'current', 'definition'));
    }

    public function getListing()
    {
        $current = $this->model()->findOrFail(request('node', 1));
        $children = $current->children();
        $children = $children->withCount('children')->defaultOrder()->paginate(20);
        $head = $this->head();
        $definition = $this;

        $children->map(function ($item, $key) use ($head, $definition) {
            $item->fields = clone $head;
            $item->fields->map(function ($item2, $key) use ($item, $definition) {
                $item->fields[$key] = clone $item2;
                $item2->setValue($item);

                $item->fields[$key]->value = $item2->getValueForList($definition);
            });
        });

        return $children;
    }

    protected function getSingleRow($recordNew)
    {
        $list = new Listing($this);
        $head = $list->head();
        $definition = $this;

        $recordNew->fields = clone $head;
        $head->map(function ($item2, $key) use ($recordNew, $definition) {
            $item2->setValue($recordNew);
            $recordNew->fields[$key]->value = $item2->getValueForList($definition);
        });

        return view('admin::addition_tree.list.single_row',
            [
                'list' => $list,
                'record' => $recordNew
            ]
        )->render();
    }

    public function changeOrder($requestOrder, $params) : array
    {
        $definition = $this;

        return (new TreeController($definition))->doChangePosition();
    }

    public function saveAddForm($request) : array
    {
        $result = parent::saveAddForm($request);

        $node = request('__node') ? : 1;
        $thisRecord = $this->model()->find($result['id']);

        $thisRecord->parent_id = $node;
        $thisRecord->save();

        $root = $this->model()->find($node);
        $thisRecord->prependToNode($root)->save();

        return $result;
    }

    public function clone(int $id): array
    {
        return $this->cloneTree($id);
    }
}
