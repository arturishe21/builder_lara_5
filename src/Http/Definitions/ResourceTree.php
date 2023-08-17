<?php

namespace Vis\Builder\Http\Definitions;

use App\Models\Tree;

class ResourceTree extends Resource
{
    public string $model = Tree::class;
    public string $title = 'Дерево сайта';
    protected string $titleDefinition;
    protected string $action = 'HomeController@showPage';

    public function getNameDefinition(): string
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

    public function saveEditForm($request): array
    {
        $record = $this->model()->withCount('children')->find($request['id']);
        $item = $this->saveActive($record, $request);
        $definition = $this;

        return [
            'id' => $item->id,
            'html' => view('admin::tree.row', compact('item', 'definition'))->render()
        ];
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function showTemplate(): ?array
    {
        return null;
    }
}
