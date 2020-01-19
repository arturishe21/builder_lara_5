<?php

namespace Vis\Builder\Fields;

/**
 * Class TextareaField.
 */
class TextareaField extends AbstractField
{
    /**
     * @return bool
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * @param $db
     * @param $value
     */
    public function onSearchFilter(&$db, $value)
    {
        $table = $this->definition['db']['table'];
        $tabs = $this->getAttribute('tabs');
        if ($tabs) {
            $field = $table.'.'.$this->getFieldName();
            $db->where(function ($query) use ($field, $value, $tabs) {
                foreach ($tabs as $tab) {
                    $query->orWhere($field.$tab['postfix'], 'LIKE', '%'.$value.'%');
                }
            });
        } else {
            $db->where($table.'.'.$this->getFieldName(), 'LIKE', '%'.$value.'%');
        }
    }

    /**
     * @return string
     */
    public function getLabelClass()
    {
        return 'textarea';
    }
}
