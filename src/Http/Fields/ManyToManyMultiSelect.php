<?php

namespace Vis\Builder\Fields;

use Vis\Builder\Definitions\Resource;

class ManyToManyMultiSelect extends ManyToMany
{
    public function save($collectionString, $model)
    {
        $model->{$this->options->getRelation()}()->detach();

        if (is_array($collectionString)) {
           $model->{$this->options->getRelation()}()->syncWithoutDetaching($collectionString);
        }
    }

    public function getOptionsSelected(Resource $definition)
    {
        if (request()->id) {

            $tableRelateModel = $definition->model()->find(request()->id)
                ->{$this->options->getRelation()}()->getRelated()->getTable();

            $selected = $definition->model()->find(request()->id)->{$this->options->getRelation()}()
                ->select(["{$tableRelateModel}.id", "{$tableRelateModel}.{$this->options->getKeyField()} as name"])
                ->get();

            $result = [];

            foreach ($selected as $item) {
                $result[$item->id] = $item->name;
            }

            return $result;
        }

        return [];
    }

}
