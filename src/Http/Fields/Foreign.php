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
            $data[$item->id] = $item->t('name');
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
        $definition = $this->getDefinition($definition);
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
        $definition = $this->getDefinition($definition);
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $record = $modelRelated::select(['id', $this->options->getKeyField() . ' as name']);

        $recordThis = $record->rememberForever()
                             ->cacheTags($this->getCacheArray($definition, $modelRelated))
                             ->find($value);

        return optional($recordThis)->t('name');
    }

    private function getCacheArray($definition, $modelRelated)
    {
        $cacheArray[] = $definition->getCacheKey();
        $cacheArray[] = $modelRelated->getTable();

        return $cacheArray;
    }

    private function getDefinition($definition)
    {
        if (request('paramsJson')) {
            $listParams = json_decode(request('paramsJson'));
            $definitionPath = $listParams->path_definition;

            return new $definitionPath();
        }

        return $definition;
    }
}
