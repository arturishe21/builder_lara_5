<?php

namespace Vis\Builder\Fields;

use Vis\Builder\Definitions\Resource;

class ManyToManyMultiSelect extends ManyToMany
{
    public function save($collectionArray, $model)
    {
        $model->{$this->options->getRelation()}()->detach();

        if (is_array($collectionArray) && $collectionArray[0]) {
           $model->{$this->options->getRelation()}()->syncWithoutDetaching($collectionArray);
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
                $result[$item->id] = $item->t('name');
            }

            return $result;
        }

        return [];
    }

}
