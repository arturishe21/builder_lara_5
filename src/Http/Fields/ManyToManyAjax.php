<?php

namespace Vis\Builder\Fields;

use Vis\Builder\Definitions\Resource;

class ManyToManyAjax extends ManyToMany
{
    public function search(Resource $definition) : array
    {
        return [
            'results' => $this->getOptions($definition),
        ];
    }

    public function getOptions(Resource $definition) : array
    {
        return $this->getDataWithWhereAndOrder($definition)->toArray();
    }

    public function save($collectionString, $model)
    {
        $collectionString = explode(',', $collectionString);

        $model->{$this->options->getRelation()}()->detach();

        $model->{$this->options->getRelation()}()->syncWithoutDetaching($collectionString);
    }

    public function getOptionsSelected(Resource $definition)
    {
        if (request()->id) {
            $table = $definition->model()->{$this->options->getRelation()}()->getRelated()->getTable();

            $selected = $definition->model()->find(request()->id)->{$this->options->getRelation()}()
                ->select([ "{$table}.id", "{$table}.{$this->options->getKeyField()} as name"])->get()->toArray();

            return $selected;
        }

        return;
    }
}
