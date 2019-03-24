<?php

namespace Vis\Builder\System;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Vis\Builder\Fields\AbstractField;
use Vis\Builder\Handlers\CustomHandler;

abstract class AbstractDefinition
{
    protected $handler;

    abstract public function getModel() : Model;

    abstract public function getCaption() : string;

    abstract public function getFields() : array;

    final public function getCustomHandler() : ?CustomHandler
    {
        if (is_string($this->handler) && class_exists($this->handler)) {
            return new ($this->handler);
        }

        return null;
    }

    final public function hasCallbacks() : bool
    {
        return !empty($this->getCallbacks());
    }

    final public function hasButtons() : bool
    {
        return !empty($this->getButtons());
    }

    final public function hasImportButtons() : bool
    {
        return !empty($this->getImportButtons());
    }

    final public function hasExportButtons() : bool
    {
        return !empty($this->getExportButtons());
    }

    final public function hasMultiActions() : bool
    {
        return !empty($this->getMultiActions());
    }

    public function isSortable() : bool
    {
        return false;
    }

    public function getCallbacks() : array
    {
        return [];
    }

    public function getOrder() : array
    {
        return ['id', 'desc'];
    }

    public function getFilter(Builder $builder) : Builder
    {
        return $builder;
    }

    public function getButtons() : array
    {
        return [];
    }

    public function getActions() : array
    {
        return [];
    }

    public function getExportButtons() : array
    {
        return [];
    }

    public function getImportButtons() : array
    {
        return [];
    }

    public function getMultiActions() : array
    {
        return [];
    }

    public function getFieldsList()
    {
        $result = [];

        foreach ($this->getFields() as $name => $value) {
            if ($this->checkShowList($value)) {
                $result[] = $value;
            }
        }

        return $result;
    }

    public function isFilterPresent()
    {
        $fieldsList = $this->getFieldsList();

        foreach ($fieldsList as $field) {
            if ($field->getAttribute('filter')) {
                return true;
            }
        }

        return false;
    }

    private function checkShowList(AbstractField $value)
    {
        if (in_array($value->getAttribute('type'), ['pattern', 'definition', 'hidden'])) {
            return false;
        }

        if ($value->getAttribute('hide_list', false)) {
            return false;
        }

        return true;
    }
}
