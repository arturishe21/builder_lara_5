<?php

namespace Vis\Builder\Definitions;

use Vis\Builder\ControllersNew\TreeController;
use Vis\Builder\Services\Listing;

class ResourceAdditionTree extends Resource
{
    public function getTableView()
    {
        return 'admin::new.addition_tree.table';
    }

    public function getList()
    {
        $list = new Listing($this);
        $listingRecords = $list->body();
        $current = $this->model()->findOrFail(request('node', 1));
        $definition = $this;

        return view('admin::new.addition_tree.list.table',
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

        return view('admin::new.addition_tree.list.single_row',
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

    protected function saveActive($record, $request)
    {
        parent::saveActive($record, $request);

        $record->parent_id = request('__node', 1);
        $record->save();

        $root = $this->model()->find(request('__node', 1));
        $record->prependToNode($root)->save();

        return $record;
    }

}
