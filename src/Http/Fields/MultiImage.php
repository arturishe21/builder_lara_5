<?php

namespace Vis\Builder\Fields;

class MultiImage extends Image
{
    public $onlyForm = true;

    public function getValueForList($definition)
    {
        return '';
    }

    public function getValueArray()
    {
        return json_decode($this->getValue());
    }
}
