<?php

namespace Vis\Builder\Fields;

use Illuminate\Database\Eloquent\Builder;

class CheckboxField extends AbstractField
{
    public function isEditable()
    {
        return true;
    }

    public function prepareQueryValue($value)
    {
        if (! $value && $this->getAttribute('is_null')) {
            return null;
        }

        return $value ? 1 : 0;
    }

    public function onSearchFilter(Builder $builder, $value)
    {
        $builder->where($this->definition->getTable().'.'.$this->getFieldName(), '=', $value);
    }

    public function getFilterInput()
    {
        if (!$this->getAttribute('filter')) {
            return null;
        }

        $definitionName = $this->definition->getName();
        $sessionPath = 'table_builder.'.$definitionName.'.filters.'.$this->getFieldName();
        $filter = session($sessionPath, '');

        $table = view('admin::tb.filter_checkbox');
        $table->filter = $filter;
        $table->name = $this->getFieldName();
        $table->options = $this->getAttribute('options');

        return $table->render();
    }

    public function getEditInput($row = [])
    {
        if ($this->hasCustomHandlerMethod('onGetEditInput')) {
            $res = $this->handler->onGetEditInput($this, $row);

            if ($res) {
                return $res;
            }
        }

        $table = view('admin::tb.input_checkbox');
        $table->value = $this->getValue($row);
        $table->name = $this->getFieldName();
        $table->caption = $this->getAttribute('caption');
        $table->disabled = $this->getAttribute('disabled');

        return $table->render();
    }

    public function getValueExport($row)
    {
        return $this->getValue($row) ? 'Да' : 'Нет';
    }

    public function getListValue($row)
    {
        if ($this->hasCustomHandlerMethod('onGetListValue')) {
            $res = $this->handler->onGetListValue($this, $row);

            if ($res) {
                return $res;
            }
        }

        return view('admin::tb.input_checkbox_list')->with('is_checked', $this->getValue($row));
    }

    public function getListValueFastEdit($row, $ident)
    {
        $field = $this;

        return view('admin::tb.fast_edit_checkbox', compact('row', 'ident', 'field'));
    }

    public function getValue($row, $postfix = '')
    {
        if ($this->hasCustomHandlerMethod('onGetValue')) {
            $res = $this->handler->onGetValue($this, $row, $postfix);

            if (is_int($res)) {
                return $res;
            }
        }

        $isset = isset($row[$this->getFieldName()]);

        return ($isset && $row[$this->getFieldName()]) || (!$isset && !$this->getAttribute('not_checked_default', false)) ? 1 : 0;
    }

    public function notChecked(bool $is = true)
    {
        $this->attributes['not_checked_default'] = $is;

        return $this;
    }
}
