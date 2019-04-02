<?php

namespace Vis\Builder\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Vis\Builder\Fields\AbstractField;
use Vis\Builder\Handlers\CustomHandler;

abstract class AbstractDefinition
{
    protected $handler;
    protected $caption;
    protected $perPage;
    protected $position;
    protected $extends;
    protected $action_url_tree;
    protected $cards = [];
    protected $sortable = false;
    protected $actions = [
        'insert' => [
            'caption' => 'Добавить'
        ],
        'preview' => [
            'caption' => 'Предпросмотр'
        ],
        'clone' => [
            'caption' => 'Клонировать'
        ],
        'update' => [
            'caption' => 'Редактировать'
        ],
        'revisions' => [
            'caption' => 'Версии'
        ],
        'delete' => [
            'caption' => 'Удалить'
        ],
    ];
    protected $cache = [
        'tags' => [],
        'keys' => []
    ];

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
        return substr(Str::snake(class_basename($this)), 0,-11);
    }

    final public function isSortable() : bool
    {
        return $this->sortable;
    }

    final public function hasAction($action) : bool
    {
        return isset($this->getActions()[$action]);
    }

    final public function getCards() : array
    {
        return $this->cards;
    }

    final public function getFieldsByName() : array
    {
        $fields = [];

        foreach ($this->getFields() as $field) {
            $fields[$field->getFieldName()] = $field;
        }

        return $fields;
    }

    final public function getPosition() : ?array
    {
        return $this->position;
    }

    final public function getCacheTags() : array
    {
        return $this->cache['tags'] ?? [];
    }

    final public function getCacheKeys() : array
    {
        return $this->cache['keys'] ?? [];
    }

    final public function getTable() : string
    {
        return $this->getModel()->getTable();
    }

    final public function getExtendsTable() : array
    {
        return $this->extends;
    }

    final public function getActionUrlTree()
    {
        return $this->action_url_tree;
    }

    public function getActions() : array
    {
        return $this->actions;
    }

    public function getOrder() : array
    {
        return ['id', 'desc'];
    }

    public function getPaginationQuantityButtons() : array
    {
        return [];
    }

    public function getCallbacks() : array
    {
        return [];
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

        foreach ($this->getFieldsByName() as $name => $field) {
            if ($this->checkShowList($field)) {
                $result[$name] = $field;
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
