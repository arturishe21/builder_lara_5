<?php

namespace Vis\Builder\Fields;

class Checkbox extends Field
{
    public function getValueForList($definition) : string
    {
        if ($this->fastEdit) {

            $idRecord = $this->getId();
            $isChecked = $this->getValue();
            $field = $this->getNameFieldInBd();

            return view('admin::list.fast_edit.checkbox', compact('idRecord', 'isChecked', 'field'));
        }

        if ($this->value == 1) {
            return '<span class="glyphicon glyphicon-ok"></span>';
        }

        return '<span class="glyphicon glyphicon-minus"></span>';
    }

    public function getValueForExel($definition)
    {
        return $this->value;
    }

    public function getOptions() : array
    {
        return [
            '0' => __cms('Нет'),
            '1' => __cms('Да')
        ];
    }

    public function getValue()
    {
        if ($this->value === '' && $this->defaultValue) {
            return true;
        }

        if ($this->value) {
            return true;
        }

        return false;
    }
}
