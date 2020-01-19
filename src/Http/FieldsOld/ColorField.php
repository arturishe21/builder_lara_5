<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\View;

/**
 * Class ColorField.
 */
class ColorField extends AbstractField
{
    /**
     * @param $db
     * @param $value
     */
    public function onSearchFilter(&$db, $value)
    {
        $table = $this->definition['db']['table'];
        $db->where($table.'.'.$this->getFieldName(), 'LIKE', '%'.$value.'%');
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

        return '<span style="height: 20px; width:20px; display:inline-block; background-color: '.$this->getValue($row).';"></spans>';
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

        $input = view('admin::tb.input_color');
        $input->value = $this->getValue($row);
        $input->name = $this->getFieldName();
        $input->type = $this->getAttribute('color_type', 'hex');
        $input->default = $this->getAttribute('default', '');
        $input->comment = $this->getAttribute('comment');

        return $input->render();
    }
}
