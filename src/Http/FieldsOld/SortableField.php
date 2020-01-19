<?php

namespace Vis\Builder\Fields;

class SortableField extends AbstractField
{
    public $list;
    public $optionActivation;
    public $main;

    public function onSearchFilter(&$db, $value)
    {
        $preparedValue = str_replace(' ', '%', $value);
        $preparedColumn = "{$this->definition['db']['table']}.{$this->getFieldName()}";

        $db->where($preparedColumn, 'LIKE', "%$preparedValue%");
    }

    public function onSelectValue(&$query)
    {
        $tableName = $this->definition['db']['table'];
        $fieldName = $this->getFieldName();

        $query->addSelect("$tableName.$fieldName");
    }

    public function getEditInput($row = [])
    {
        $input = view('admin::tb.input_sortable');
        $fieldName = $this->getFieldName();

        $input->name = $fieldName;
        $input->fieldValue = $row[$fieldName] ? explode(',', $row[$fieldName]) : [];
        $input->add_checkbox = $this->getAttribute('add_checkbox', false);
        $input->is_sortable = $this->getAttribute('is_sortable', true);
        $input->is_checked = $this->getAttribute('is_checked', true);
        $input->main_field = $this->getAttribute('main', false);

        $list = $this->getAttribute('list', []);

        if (is_callable($list)) {
            $list = $list($row);
        }

        $input->list = $list;

        return $input->render();
    }

    public function getListValue($row)
    {
        if ($this->hasCustomHandlerMethod('onGetListValue')) {
            $res = $this->handler->onGetListValue($this, $row);
            if ($res) {
                return $res;
            }
        }

        return $row[$this->getFieldName()] ?? null;
    }
}
