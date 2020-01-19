<?php

namespace Vis\Builder\Definitions;

use App\Models\Tree;

class ResourceTree extends Resource
{
    public $model = Tree::class;
    public $title = 'Дерево сайта';
    protected $titleDefinition;
    protected $action = 'HomeController@showPage';

    public function getNameDefinition() : string
    {
        return 'tree';
    }

    public function getTitleDefinition(): string
    {
        if ($this->titleDefinition) {
            return $this->titleDefinition;
        }

        return parent::getNameDefinition();
    }

    public function saveEditForm($request)
    {
        $record = $this->model()->find($request['id']);
        $item = $this->saveActive($record, $request);
        $definition = $this;

        return [
            'id' => $item->id,
            'html' => view('admin::new.tree.row', compact('item', 'definition'))->render()
        ];
    }

    public function getAction()
    {
        return $this->action;
    }

}
