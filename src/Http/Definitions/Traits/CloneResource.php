<?php

namespace Vis\Builder\Http\Definitions\Traits;

use Illuminate\Http\JsonResponse;

trait CloneResource
{
    public function cloneTree(int $id): JsonResponse
    {
        $this->cloneRecursively($id);

        $this->clearCache();

        return $this->returnSuccess();
    }

    private function cloneRecursively(int $id, int $parentId = 0): void
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
    }

    public function clone(int $id): JsonResponse
    {
        $this->model()->find($id)->duplicate();

        return $this->returnSuccess();
    }

}
