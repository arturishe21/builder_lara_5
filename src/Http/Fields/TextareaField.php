<?php

namespace Vis\Builder\Fields;

use Illuminate\Database\Eloquent\Builder;

class TextareaField extends AbstractField
{
    public function isEditable()
    {
        return true;
    }

    public function onSearchFilter(Builder $builder, $value)
    {
        $table = $this->definition['db']['table'];
        $tabs = $this->getAttribute('tabs');
        if ($tabs) {
            $field = $table.'.'.$this->getFieldName();
            $builder->where(function ($query) use ($field, $value, $tabs) {
                foreach ($tabs as $tab) {
                    $query->orWhere($field.$tab['postfix'], 'LIKE', '%'.$value.'%');
                }
            });
        } else {
            $builder->where($table.'.'.$this->getFieldName(), 'LIKE', '%'.$value.'%');
        }
    }

    public function getLabelClass()
    {
        return 'textarea';
    }

    public function rows(int $qty)
    {
        $this->attributes['rows'] = $qty;

        return $this;
    }
}
