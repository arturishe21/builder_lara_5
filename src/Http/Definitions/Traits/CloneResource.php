<?php

namespace Vis\Builder\Definitions\Traits;

use Illuminate\Support\Facades\Cache;

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

        $page = $model::where('id', $id)->select('*')->first()->toArray();
        $idClonePage = $page['id'];
        unset($page['id']);
        if ($parentId) {
            $page['parent_id'] = $parentId;
        }

        if ($page['parent_id']) {
            $root = $model::find($page['parent_id']);

            $rec = new $model();
            $countPages = $model::where('parent_id', $page['parent_id'])->where('slug', $page['slug'])->count();

            if ($countPages) {
                $page['slug'] = $parentId ?
                    $page['slug'].'_'.$page['parent_id'] :
                    $page['slug'].'_'.time();
            }

            foreach ($page as $k => $val) {
                $rec->$k = $val;
            }

            $rec->save();
            $lastId = $rec->id;

            $rec->makeChildOf($root);
        }

        $folderCheck = $model::where('parent_id', $idClonePage)->select('*')->orderBy('lft', 'desc')->get()->toArray();
        if (count($folderCheck)) {
            foreach ($folderCheck as $pageChild) {
                $this->cloneRecursively($pageChild['id'], $lastId);
            }
        }
    }

    public function clone(int $id) : array
    {
        $this->model()->find($id)->replicate()->push();

        return $this->returnSuccess();
    }

}
