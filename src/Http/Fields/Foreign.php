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

        if (isset($request[$nameField]) && $request[$nameField]) {
            return $request[$nameField];
        }

        return null;
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

        return $collection->rememberForever()->cacheTags($this->getCacheArray($definition, $modelRelated))->get();
    }

    public function getValueForList($definition)
    {
        $value = $this->getValue();
        $optionsArray = $this->getOptions($definition);

        if ($this->fastEdit) {

            $idRecord = $this->getId();
            $field = $this->getNameFieldInBd();

            return view('admin::list.fast_edit.select', compact('idRecord', 'value', 'field', 'optionsArray'));
        }
        
        $definition = $this->getDefinition($definition);
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $record = $modelRelated::select(['id', $this->options->getKeyField() . ' as name']);

        $recordThis = $record->rememberForever()
                             ->cacheTags($this->getCacheArray($definition, $modelRelated))
                             ->find($value);

        return optional($recordThis)->name;
    }

    public function getValueForExel($definition)
    {
        return $this->getValueForList($definition);
    }

    protected function getCacheArray($definition, $modelRelated)
    {
        $cacheArray[] = $definition->getCacheKey();
        $cacheArray[] = $modelRelated->getTable();

        return $cacheArray;
    }

    protected function getDefinition($definition)
    {
        if (request('paramsJson')) {
            $listParams = json_decode(request('paramsJson'));
            $definitionPath = $listParams->path_definition;

            return new $definitionPath();
        }

        return $definition;
    }
}
