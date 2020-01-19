<?php

namespace Vis\Builder\FieldsNew;

class MultiImage extends Image
{
    public $onlyForm = true;

    public function getValueForList($definition)
    {
        return '';
    }

    public function getValueArray()
    {
        $value = $this->getValue();

        return json_decode($value);
    }
}
