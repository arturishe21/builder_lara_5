<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

/**
 * Class SelectField.
 */
class SelectField extends AbstractField
{
    /**
     * @return bool
     */
    public function isEditable()
    {
        return true;
    }

    // end isEditable

    /**
     * @param $db
     * @param $value
     */
    public function onSearchFilter(&$db, $value)
    {
        $table = $this->definition['db']['table'];
        $db->where($table.'.'.$this->getFieldName(), '=', $value);
    }

    // end onSearchFilter

    /**
     * @throws \Throwable
     *
     * @return string
     */
    public function getFilterInput()
    {
        if (! $this->getAttribute('filter')) {
            return '';
        }

        $definitionName = $this->getOption('def_name');
        $sessionPath = 'table_builder.'.$definitionName.'.filters.'.$this->getFieldName();

        $filter = Session::get($sessionPath, '');

        $table = view('admin::tb.filter_select');
        $table->filter = $filter;
        $table->name = $this->getFieldName();

        $table->options = $this->getAttributeCallable('options');

        return $table->render();
    }

    // end getFilterInput

    /**
     * @param array $row
     *
     * @throws \Throwable
     *
     * @return string
     */
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
        $table->options = $this->getAttributeCallable('options');
        $table->disabled = $this->getAttribute('disabled');

        $table->action = $this->getAttribute('action');
        $table->readonly_for_edit = $this->getAttribute('readonly_for_edit');

        return $table->render();
    }

    // end getEditInput

    /**
     * @param $row
     *
     * @return bool
     */
    public function getListValue($row)
    {
        if ($this->hasCustomHandlerMethod('onGetListValue')) {
            $res = $this->handler->onGetListValue($this, $row);
            if ($res) {
                return $res;
            }
        }

        $val = $this->getValue($row);
        $options = $this->getAttributeCallable('options');

        return isset($options[$val]) ? $options[$val] : $val;
    }

    // end getListValue

    /**
     * @param $row
     *
     * @return string
     */
    public function getRowColor($row)
    {
        $colors = $this->getAttribute('colors');
        if ($colors) {
            return isset($colors[$this->getValue($row)]) ? $colors[$this->getValue($row)] : '';
        }
    }
}
