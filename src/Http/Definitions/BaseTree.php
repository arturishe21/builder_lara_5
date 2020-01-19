<?php

namespace Vis\Builder\Definitions;

use App\Models\Tree;

class BaseTree
{
    protected $model = Tree::class;

    public function model()
    {
        return new $this->model;
    }

    public function getTemplates()
    {
        $templatesModels = $this->templates();

        $templates = [];
        foreach ($templatesModels as $slug => $template) {
            $templates[$slug] = (new $template())->getTitleDefinition();
        }

        return $templates;
    }

    public function getTitleDefinition(string $template)
    {
        if (isset($this->templates()[$template])) {

            $classDefinition = $this->templates()[$template];

            return (new $classDefinition())->getTitleDefinition();
        }
    }
}
