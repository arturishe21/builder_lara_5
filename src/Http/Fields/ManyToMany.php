<?php

namespace Vis\Builder\Fields;

use Vis\Builder\Definitions\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Collection;

class ManyToMany extends Field
{
    protected $onlyForm = true;
    protected $isManyToMany = true;
    protected $options;

    public function options($model)
    {
        $this->options = $model;

        return $this;
    }

    public function getOptions(Resource $definition) : array
    {
        $collection = $this->getDataWithWhereAndOrder($definition);

        $data = [];

        foreach ($collection as $item) {
            $data[$item->id] = $item->t('name');
        }

        return $data;
    }

    public function getDataWithWhereAndOrder(Resource $definition)
    {
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $collection = $modelRelated::select(['id', $this->options->getKeyField().' as name']);
        $where = $this->options->getWhereCollection();
        $order = $this->options->getOrderCollection();

        if (count($where)) {
            foreach ($where as $param) {
                $collection = $collection->where($param['field'], $param['eq'], $param['value']);
            }
        }

        if (count($order)) {
            foreach ($order as $param) {
                $collection = $collection->orderBy($param['field'], $param['order']);
            }
        }

        if (request()->q) {
            $collection = $collection->where($this->options->getKeyField(), 'like', request()->q . '%');
        }

        return $collection->get();
    }

    public function getOptionsSelected(Resource $definition)
    {
        if (request()->id) {
            $selected = $definition->model()->find(request()->id)->{$this->options->getRelation()}()->get();

            $selectedIds = $selected->map(function ($item) {
                return $item->id;
            });

            return $selectedIds->toArray();
        }

        return [];
    }

    public function save($collectionString, $model)
    {
        $collectionArray = explode(',', $collectionString);

        $model->{$this->options->getRelation()}()->detach();

        if ($collectionString) {
            $model->{$this->options->getRelation()}()->syncWithoutDetaching($collectionArray);
        }
    }

    public function getNameField() : string
    {
        return str_replace('-', '', Str::slug(parent::getNameField()));
    }
}
