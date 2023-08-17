<?php

namespace Vis\Builder\Http\Fields\Relations;

class Options
{
    protected string $relation;
    protected array $whereCollection = [];
    protected array $orderCollection = [];
    protected string $keyField = 'title';
    protected bool $isJson = false;

    public function __construct(string $relation)
    {
        $this->relation = $relation;
    }

    public function isJson($isJson = true): self
    {
        $this->isJson = $isJson;

        return $this;
    }

    public function where(string $field, string $eq, string $value): self
    {
        $this->whereCollection[] = [
            'field' => $field,
            'eq' => $eq,
            'value' => $value
        ];

        return $this;
    }

    public function orderBy(string $field, string $order = 'desc'): self
    {
        $this->orderCollection[] = [
            'field' => $field,
            'order' => $order
        ];

        return $this;
    }

    public function keyField(string $field = 'title'): self
    {
        $this->keyField = $field;

        return $this;
    }

    public function getWhereCollection(): array
    {
        return $this->whereCollection;
    }

    public function getOrderCollection(): array
    {
        return $this->orderCollection;
    }

    public function getKeyField(): string
    {
        if ($this->isJson) {
            return $this->keyField . '->' . defaultLanguage();
        }

        return $this->keyField;
    }

    public function getRelation(): string
    {
        return $this->relation;
    }
}
