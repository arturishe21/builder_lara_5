<?php

namespace Vis\Builder\Fields;

use Illuminate\Database\Eloquent\Builder;

class SelectField extends AbstractField
{
    public function isEditable()
    {
        return true;
    }

    public function onSearchFilter(Builder $builder, $value)
    {
        $builder->where($this->definition->getTable().'.'.$this->getFieldName(), '=', $value);
    }

    public function getFilterInput()
    {
        if (! $this->getAttribute('filter')) {
            return '';
        }

        $definitionName = $this->definition->getName();
        $sessionPath = 'table_builder.'.$definitionName.'.filters.'.$this->getFieldName();
        $filter = session($sessionPath, '');

        $table = view('admin::tb.filter_select');
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

        $table = view('admin::tb.input_select');
        $table->selected = $this->getValue($row);
        $table->name = $this->getFieldName();
        $table->disabled = $this->getAttribute('disabled');
        $table->action = $this->getAttribute('action');
        $table->readonly_for_edit = $this->getAttribute('readonly_for_edit');
        $table->comment = $this->getAttribute('comment', null);

        $options = $this->getAttribute('options');

        if (is_callable($options)) {
            $table->options = $options();
        } else {
            $table->options = $this->getAttribute('options');
        }

        return $table->render();
    }

    public function getListValue($row)
    {
        if ($this->hasCustomHandlerMethod('onGetListValue')) {
            $res = $this->handler->onGetListValue($this, $row);

            if ($res) {
                return $res;
            }
        }

        $val = $this->getValue($row);
        $optionsRes = $this->getAttribute('options');

        if (is_callable($optionsRes)) {
            $options = $optionsRes();
        } else {
            $options = $optionsRes;
        }

        if (isset($options[$val])) {
            return $options[$val];
        } else {
            return $val;
        }
    }

    public function getRowColor($row)
    {
        $colors = $this->getAttribute('colors');

        if ($colors) {
            return isset($colors[$this->getValue($row)]) ? $colors[$this->getValue($row)] : '';
        }
    }

    public function options($options)
    {
        $this->attributes['options'] = $options;

        return $this;
    }

    public function colors(array $colors)
    {
        $this->attributes['colors'] = $colors;

        return $this;
    }

    public function comment(string $comment)
    {
        $this->attributes['comment'] = $comment;

        return $this;
    }

    public function isTRColor(bool $is = true)
    {
        $this->attributes['is_tr_color'] = $is;

        return $this;
    }

    public function action(bool $is = true)
    {
        $this->attributes['action'] = $is;

        return $this;
    }
}
