<?php

namespace Vis\Builder\Definitions;

use App\Models\Tree;
use Vis\Builder\Services\Listing;

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

    public function getTitleDefinition()
    {
        if ($this->titleDefinition) {
            return $this->titleDefinition;
        }

        return parent::getNameDefinition();
    }

    public function saveEditForm($request) : array
    {
        $record = $this->model()->withCount('children')->find($request['id']);
        $item = $this->saveActive($record, $request);
        $definition = $this;

        return [
            'id' => $item->id,
            'html' => view('admin::tree.row', compact('item', 'definition'))->render()
        ];
    }

    public function getAction()
    {
        return $this->action;
    }

    public function showTemplate() : ?array
    {
        return null;
    }

}
