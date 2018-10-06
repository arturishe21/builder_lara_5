<?php

namespace Vis\Builder\Fields;

use Illuminate\Database\Query\Builder;

class SortableField extends AbstractField
{

    public $list;
    public $optionActivation;
    public $main;

    public function onSearchFilter(&$db, $value)
    {
        return null;
    }

    /**
     * @param Builder $db
    */
    public function onSelectValue(&$db)
    {

        $tabs = $this->getAttribute('tabs');

        $tableName = $this->definition['db']['table'];
        $fieldName = $this->getFieldName();

        if ($tabs) {
            foreach ($tabs as $tab) {

                $name = $tableName .'.'. $this->getFieldName() . $tab['postfix'];
                $db->addSelect($name);
            }

        } else {
            $db->addSelect($tableName .'.'. $fieldName);
        }
    }

    /**
     * @param array $row
     * @throws \Throwable
     * @return string
    */
    public function getEditInput($row = [])
    {

        $input = view('admin::partials.sortable_field');
        $input->name  = $this->getFieldName();

        $input->fieldValue = $row[$input->name] ? explode(',', $row[$input->name]) : [];
        $input->store  = $this->getAttribute('store');
        $input->optionActivation  = $this->getAttribute('option_activation', false);
        $input->is_sortable = $this->getAttribute('is_sortable', true);
        $input->is_checked = $this->getAttribute('is_checked', true);

        $input->main_field = $this->getAttribute('main', false);

        $list = $this->getAttribute('list', []);
        if (is_callable($list))
            $list = $list($row);

        $input->list = $list;

        return $input->render();

    }


    public function getListValue($row)
    {
        return false;
    }
}