<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\Session;

/**
 * Class DateField.
 */
class DateField extends AbstractField
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

            return date('Y-m-d');
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

        $dateTimeArray = explode(' ', $this->getValue($row));

        return $dateTimeArray[0] ?? $this->getValue($row);
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

        $value = $this->getValue($row);

        $input = view('admin::tb.input_date');
        $input->value = $value;
        $input->name = $this->getFieldName();
        $input->months = $this->getAttribute('months');
        $input->prefix = $row ? 'e-' : 'c-';
        $input->comment = $this->getAttribute('comment');
        $input->disabled = $this->getAttribute('disabled');

        return $input->render();
    }

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
