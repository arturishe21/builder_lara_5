<?php

namespace Vis\Builder\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Vis\Builder\Fields\AbstractField;
use Vis\Builder\Handlers\CustomHandler;

abstract class AbstractDefinition implements \ArrayAccess
{
    protected $handler;
    protected $caption;
    protected $perPage;

    abstract public function getModel() : Model;

    abstract public function getFields() : array;

    final public function getCustomHandler() : ?CustomHandler
    {
        if (is_string($this->handler) && class_exists($this->handler)) {
            return new $this->handler;
        }

        return null;
    }

    final public function getCaption() : string
    {
        return __cms($this->caption);
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

    final public function isShowInsert()
    {
        return isset($this->getActions()['insert']);
    }

    final public function getPerPage()
    {
        return $this->perPage;
    }

    final public function getName() : string
    {
        return substr(Str::snake(class_basename($this)), -11);
    }

    public function getPaginationQuantityButtons() : array
    {
        return [];
    }

    public function getCacheTags() : array
    {
        return [];
    }

    public function getExtendsTable() : array
    {
        return [];
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

    public function getFilters()
    {
        return [];
    }

    public function getAnnotations()
    {
        return null;
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

        foreach ($this->getFields() as $field) {
            if ($this->checkShowList($field)) {
                $result[$field->getFieldName()] = $field;
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

    public function offsetGet($value)
    {
        $method = 'get' . strtoupper($value);

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return null;
    }

    public function offsetUnset($value)
    {

    }

    public function offsetExists($value)
    {
        return method_exists($this, 'get' . strtoupper($value));
    }

    public function offsetSet($offset, $value)
    {

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
