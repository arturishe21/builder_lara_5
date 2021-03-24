<?php

namespace Vis\Builder\Definitions\Traits;

trait CloneResource
{
    public function cloneTree(int $id): array
    {
        $this->cloneRecursively($id);

        $this->clearCache();

        return $this->returnSuccess();
    }

    private function cloneRecursively($id, $parentId = '')
    {
        $model = $this->model();

        $pageOld = $model->find($id);

        $parentId = $parentId ?: $pageOld->parent_id;

        $root = $model::find($parentId);

        $page = $model->find($id)->duplicate();
        $page->makeChildOf($root);

        $countPages = $model::where('parent_id', $page->parent_id)->where('slug', $page->slug)->count();

        if ($countPages) {
            $page->slug = $page->slug. '_' .time();
            $page->save();
        }

        $folderCheck = $model::where('parent_id', $pageOld->id)->orderBy('lft', 'desc')->get();

        if (count($folderCheck)) {
            foreach ($folderCheck as $pageChild) {
                $this->cloneRecursively($pageChild->id, $page->id);
            }
        }

        return;
    }

    public function clone(int $id) : array
    {
        $this->model()->find($id)->duplicate();

        return $this->returnSuccess();
    }

}
