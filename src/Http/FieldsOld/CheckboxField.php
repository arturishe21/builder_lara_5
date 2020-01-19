<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\Session;

/**
 * Class CheckboxField.
 */
class CheckboxField extends AbstractField
{
    /**
     * @return bool
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * @param $value
     *
     * @return string|void
     */
    public function prepareQueryValue($value)
    {
        if (! $value && $this->getAttribute('is_null')) {
            return;
        }

        return $value ? '1' : '0';
    }

    /**
     * @param $db
     * @param $value
     */
    public function onSearchFilter(&$db, $value)
    {
        $table = $this->definition['db']['table'];
        $db->where($table.'.'.$this->getFieldName(), '=', $value);
    }

    /**
     * @throws \Throwable
     *
     * @return string|void
     */
    public function getFilterInput()
    {
        if (! $this->getAttribute('filter')) {
            return;
        }

        $definitionName = $this->getOption('def_name');
        $sessionPath = 'table_builder.'.$definitionName.'.filters.'.$this->getFieldName();
        $filter = Session::get($sessionPath, '');

        $table = view('admin::tb.filter_checkbox');
        $table->filter = $filter;
        $table->name = $this->getFieldName();
        $table->options = $this->getAttribute('options');

        return $table->render();
    }

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

        $table = view('admin::tb.input_checkbox');
        $table->value = $this->getValue($row);
        $table->name = $this->getFieldName();
        $table->caption = $this->getAttribute('caption');
        $table->disabled = $this->getAttribute('disabled');

        return $table->render();
    }

    /**
     * @param $row
     *
     * @return string
     */
    public function getValueExport($row)
    {
        return $this->getValue($row) ? 'Да' : 'Нет';
    }

    /**
     * @param $row
     *
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
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

    /**
     * @param $row
     * @param $ident
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getListValueFastEdit($row, $ident)
    {
        $field = $this;

        return view('admin::tb.fast_edit_checkbox', compact('row', 'ident', 'field'));
    }

    /**
     * @param $row
     * @param string $postfix
     *
     * @return bool|string
     */
    public function getValue($row, $postfix = '')
    {
        if ($this->hasCustomHandlerMethod('onGetValue')) {
            $res = $this->handler->onGetValue($this, $row, $postfix);

            if (is_int($res)) {
                return $res;
            }
        }

        return (
            (isset($row[$this->getFieldName()]) && $row[$this->getFieldName()]) ||
            (! isset($row[$this->getFieldName()]) && $this->getAttribute('not_checked_default') !== true)

                )
                    ? '1' : '0';
    }
}
