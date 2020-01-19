<?php

namespace Vis\Builder\FieldsNew;

use Illuminate\Support\Arr;
use Vis\Builder\Definitions\Resource;

class Foreign extends Field
{
    protected $options = [];

    public function options($model)
    {
        $this->options = $model;

        return $this;
    }

    public function getOptions($definition)
    {
        $collection = $this->getDataWithWhereAndOrder($definition);
        $data = [];
        foreach ($collection as $item) {
            $data[$item->id] = $item->name;
        }

        return $data;
    }

    public function getDataWithWhereAndOrder(Resource $definition)
    {
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $collection = $modelRelated::select(['id', $this->options->getKeyField() . ' as name']);
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

        return $collection->get();
    }


    public function getValueForList($definition)
    {
        $value = $this->getValue();
        $options = $this->getOptions($definition);

        if (isset($options[$value]) && Arr::get($options, $value)) {
            return $options[$value];
        }
    }
}
