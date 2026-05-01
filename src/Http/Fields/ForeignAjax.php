<?php

namespace Vis\Builder\Http\Fields;

use Illuminate\Http\JsonResponse;

class ForeignAjax extends Foreign
{
    public function setValue($item)
    {
        $this->value = '';
        if ($this->relationHasOne) {
            $item = $item->{$this->relationHasOne};
        }

        if (!$item) {
            return;
        }

        $relation = $item->{$this->options->getRelation()}()
            ->select([ "id", "{$this->options->getKeyField()} as name"])
            ->first();

        if ($relation) {
            $this->value = $relation->name;
        }
    }

    public function getValueForList($definition)
    {
        return $this->getValue();
    }

    public function getValueForExel($definition)
    {
        return $this->getValueForList($definition);
    }

    public function getValueForInput($definition)
    {
        $value = request()->id;
        $model = $definition->model();

        if ($value) {
            $item = $model::find($value);
            if ($item) {

                if ($this->relationHasOne) {
                    $item = $item->{$this->relationHasOne};
                }

                $related = $item->{$this->options->getRelation()}()
                    ->select([ "id", "{$this->options->getKeyField()} as name"])
                    ->first();

                return [
                    'id' => $related->id,
                    'name' => $related->name
                ];
            }

        }
    }

    public function getValueForFilter($definition, $id)
    {
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $selectOption = $modelRelated::find($id);

        return $selectOption->{$this->options->getKeyField()};
    }

    public function search($definition): JsonResponse
    {
        $keyField = $this->options->getKeyField();
        $modelRelated = $definition->model()->{$this->options->getRelation()}()->getRelated();
        $where = $this->options->getWhereCollection();

        $modelRelated = $modelRelated->where($keyField, 'like', $this->convertQuery(request()->q) . "%");

        if (count($where)) {
            foreach ($where as $param) {
                $modelRelated = $modelRelated->where($param['field'], $param['eq'], $param['value']);
            }
        }

        $result = $modelRelated->take(10)->get(['id', $keyField . ' as name'])->toArray();

        return response()->json([
            'results' => $result
        ]);
    }
}
