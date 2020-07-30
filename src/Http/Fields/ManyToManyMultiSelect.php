<?php

namespace Vis\Builder\Fields;

use Vis\Builder\Definitions\Resource;

class ManyToManyMultiSelect extends ManyToMany
{
    public function save($collectionString, $model)
    {
        $model->{$this->options->getRelation()}()->detach();

        $model->{$this->options->getRelation()}()->syncWithoutDetaching($collectionString);
    }

    public function getOptionsSelected(Resource $definition)
    {
        if (request()->id) {

            $tableRelateModel = $definition->model()->find(request()->id)
                ->{$this->options->getRelation()}()->getRelated()->getTable();

            $selected = $definition->model()->find(request()->id)
                ->{$this->options->getRelation()}()->pluck($this->options->getKeyField(), "{$tableRelateModel}.id");

            return $selected->toArray();
        }

        return [];
    }

}
