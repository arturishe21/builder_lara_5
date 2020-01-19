<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\Session;

/**
 * Class TimestampField.
 */
class TimestampField extends AbstractField
{
    /**
     * @param $db
     * @param $value
     */
    public function onSearchFilter(&$db, $value)
    {
        $table = $this->definition['db']['table'];
        if ($this->getAttribute('is_range')) {
            if (! isset($value['from']) && ! isset($value['to'])) {
                return;
            }

            $dateFrom = isset($value['from']) ? $this->getTimestamp($value['from']) : '28800';
            $dateTo = isset($value['to']) ? $this->getTimestamp($value['to']) : '2146939932';
            $db->whereBetween(
                $table.'.'.$this->getFieldName(),
                [
                    date('Y-m-d H:i:s', $dateFrom),
                    date('Y-m-d H:i:s', $dateTo),
                ]
            );
        } else {
            $db->where(
                $table.'.'.$this->getFieldName(),
                date('Y-m-d H:i:s', $this->getTimestamp($value))
            );
        }
    }

    /**
     * @param $value
     *
     * @return string|void
     */
    public function prepareQueryValue($value)
    {
        if (! $value) {
            if ($this->getAttribute('is_null')) {
                return;
            }
        }

        return date('Y-m-d H:i:s', $this->getTimestamp($value)).'.000000';
    }

    /**
     * @param $date
     *
     * @return false|int
     */
    private function getTimestamp($date)
    {
        return strtotime(str_replace('/', '-', $date));
    }

    /**
     * @param $row
     *
     * @return bool|false|string
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

        return date('d/m/Y', $this->getTimestamp($this->getValue($row)));
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
        $value = $value ? date('d/m/Y', $this->getTimestamp($value)) : '';

        $input = view('admin::tb.input_timestamp');
        $input->value = $value;
        $input->name = $this->getFieldName();
        $input->months = $this->getAttribute('months');
        $input->prefix = $row ? 'e-' : 'c-';

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

        if ($this->getAttribute('is_range')) {
            return $this->getFilterRangeInput();
        }

        $definitionName = $this->getOption('def_name');
        $sessionPath = 'table_builder.'.$definitionName.'.filters.'.$this->getFieldName();
        $filter = Session::get($sessionPath, '');

        $input = view('admin::tb.filter_datetime');
        $input->name = $this->getFieldName();
        $input->value = $filter;

        return $input->render();
    }

    /**
     * @throws \Throwable
     *
     * @return string
     */
    private function getFilterRangeInput()
    {
        $definitionName = $this->getOption('def_name');
        $sessionPath = 'table_builder.'.$definitionName.'.filters.'.$this->getFieldName();
        $filter = Session::get($sessionPath, []);

        $input = view('admin::tb.filter_timestamp_range');
        $input->name = $this->getFieldName();
        $input->valueFrom = isset($filter['from']) ? $filter['from'] : '';
        $input->valueTo = isset($filter['to']) ? $filter['to'] : '';
        $input->months = $this->getAttribute('months');

        return $input->render();
    }

    // end getFilterRangeInput
}
