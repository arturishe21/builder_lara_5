<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\View;

/**
 * Class DefinitionField.
 */
class DefinitionField extends AbstractField
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
    }

    /**
     * @param $db
     */
    public function onSelectValue(&$db)
    {
    }

    /**
     * @param string $ident
     * @param bool   $default
     *
     * @return bool
     */
    public function getAttribute($ident, $default = false)
    {
        if ($ident == 'hide_list') {
            return true;
        }

        return parent::getAttribute($ident, $default);
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

        $this->attributes['name'] = $this->getFieldName();
        $this->attributes['table'] = config('builder.tb-definitions.'.$this->getAttribute('definition').'.db.table');

        $input = view('admin::tb.input_definition');
        $input->nameDefinition = $this->getAttribute('definition');
        $input->foreignField = $this->getAttribute('foreign_field');
        $input->name = $this->getFieldName();
        $input->table = $this->attributes['table'];
        $input->attributes = json_encode($this->attributes);

        return $input->render();
    }
}
