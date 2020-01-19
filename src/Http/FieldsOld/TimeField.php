<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

/**
 * Class TimeField.
 */
class TimeField extends AbstractField
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

            $valueFrom = isset($value['from']) ? $value['from'] : '00:00';
            $valueTo = isset($value['to']) ? $value['from'] : '23:59';
            $db->whereBetween(
                $table.'.'.$this->getFieldName(),
                [$valueFrom, $valueTo]
            );
        } else {
            $db->where(
                $table.'.'.$this->getFieldName(),
                $value
            );
        }
    }

    // end onSearchFilter

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
        $value = $value ? $value : '';

        $input = view('admin::tb.input_time');
        $input->value = $value;
        $input->name = $this->getFieldName();
        $input->prefix = $row ? 'e-' : 'c-';
        $input->comment = $this->getAttribute('comment');

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

        $input = view('admin::tb.filter_time');
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

        $input = view('admin::tb.filter_time_range');
        $input->name = $this->getFieldName();
        $input->valueFrom = isset($filter['from']) ? $filter['from'] : false;
        $input->valueTo = isset($filter['to']) ? $filter['to'] : false;
        $input->months = $this->getAttribute('months');

        return $input->render();
    }
}
