<?php

namespace Vis\Builder\Fields\Relations;

class Options
{
    protected $relation;
    protected $whereCollection = [];
    protected $orderCollection = [];
    protected $keyField = 'title';
    protected $isJson = false;

    public function __construct(string $relation)
    {
        $this->relation = $relation;
    }

    public function isJson($isJson = true)
    {
        $this->isJson = $isJson;

        return $this;
    }

    public function where(string $field, string $eq, string $value) : Options
    {
        $this->whereCollection[] = [
            'field' => $field,
            'eq' => $eq,
            'value' => $value
        ];

        return $this;
    }

    public function orderBy(string $field, string $order = 'desc') : Options
    {
        $this->orderCollection[] = [
            'field' => $field,
            'order' => $order
        ];

        return $this;
    }

    public function keyField(string $field = 'title') : Options
    {
        $this->keyField = $field;

        return $this;
    }

    public function getWhereCollection() : array
    {
        return $this->whereCollection;
    }

    public function getOrderCollection() : array
    {
        return $this->orderCollection;
    }

    public function getKeyField() : string
    {
        if ($this->isJson) {
            return $this->keyField . '->' . defaultLanguage();
        }

        return $this->keyField;
    }

    public function getRelation() : string
    {
        return $this->relation;
    }
}
