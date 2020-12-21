<?php

namespace Vis\Builder\Definitions;

use App\Models\Tree;
use Illuminate\Support\Arr;

class BaseTree
{
    protected $model = Tree::class;

    public function model()
    {
        return new $this->model;
    }

    public function getTemplates() : array
    {
        $templatesModels = $this->filterTemplates($this->templates());

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

    private function filterTemplates($templatesModels)
    {
        $idNode = request('node', 1);
        $info = $this->model::find($idNode);
        $thisTemplate = $info->template;

        $showTemplateForThisNode = (new $templatesModels[$thisTemplate]())->showTemplate();

        if ($showTemplateForThisNode) {
            $templatesModels = Arr::only($templatesModels, $showTemplateForThisNode);
        }

        return $templatesModels;
    }

    public function clearCache()
    {
        $this->model()->clearCache();
    }

    public function definition()
    {
        return \App\Cms\Definitions\Tree::class;
    }
}
