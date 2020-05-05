<?php

namespace Vis\Builder\Fields;

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

        if ($this->defaultValue) {
            $data = [
                '' => $this->defaultValue
            ];
        }

        foreach ($collection as $item) {
            $data[$item->id] = $item->name;
        }

        return $data;
    }

    public function prepareSave($request)
    {
        $nameField = $this->getNameField();

        return $request[$nameField] ?: null;
    }

    public function getDataWithWhereAndOrder(Resource $definition)
    {
        if (request('paramsJson')) {
            $listParams = json_decode(request('paramsJson'));
            $definitionPath = $listParams->path_definition;

            $definition = new $definitionPath();
        }

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

        return $this->defaultValue;
    }
}
