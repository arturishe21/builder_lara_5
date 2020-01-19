<?php

namespace Vis\Builder\Fields;

/**
 * Class CheckboxField.
 */
class HiddenField extends AbstractField
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
        return $value;
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

        $table = view('admin::tb.input_hidden');
        $table->value = $this->getValue($row);
        $table->name = $this->getFieldName();

        return $table->render();
    }

    /**
     * @param $row
     *
     * @return string
     */
    public function getValueExport($row)
    {
        return $this->getValue($row);
    }

    public function getValue($row, $postfix = '')
    {
        if (is_callable($this->getAttribute('value'))) {
            return $this->getAttribute('value')();
        }

        return $this->getAttribute('value');
    }

    public function getAttribute($ident, $default = false)
    {
        if ($ident == 'hide_list') {
            return true;
        }

        return parent::getAttribute($ident, $default = false);
    }

    /**
     * @param $row
     *
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getListValue($row)
    {
    }
}
