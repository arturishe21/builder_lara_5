<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\Session;

/**
 * Class DatetimeField.
 */
class DatetimeField extends AbstractField
{
    /**
     * @param $value
     *
     * @return false|string|void
     */
    public function prepareQueryValue($value)
    {
        if (! $value) {
            if ($this->getAttribute('is_null')) {
                return;
            }

            if ($this->getFieldName() == 'created_at') {
                return date('Y-m-d H:i:s');
            }

            return '0000-00-00 00:00:00';
        }

        return $value;
    }

    /**
     * @param $row
     *
     * @return bool|string
     */
    public function getListValue($row)
    {
        if ($this->hasCustomHandlerMethod('onGetListValue')) {
            $res = $this->handler->onGetListValue($this, $row);
            if ($res) {
                return $res;
            }
        }

        if (! $this->getValue($row)) {
            return '';
        }

        return $this->getValue($row);
    }

    // end getListValue

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

        $value = $this->getValue($row);

        $input = view('admin::tb.input_datetime');
        $input->value = $value;
        $input->name = $this->getFieldName();
        $input->months = $this->getAttribute('months');
        $input->prefix = $row ? 'e-' : 'c-';
        $input->comment = $this->getAttribute('comment');
        $input->disabled = $this->getAttribute('disabled');

        return $input->render();
    }

    // end getEditInput

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

        $input = view('admin::tb.filter_'.$this->getAttribute('filter'));
        $input->name = $this->getFieldName();
        $input->value = $filter;
        $input->months = $this->getAttribute('months');

        return $input->render();
    }
}
