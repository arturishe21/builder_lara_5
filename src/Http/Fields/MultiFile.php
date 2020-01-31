<?php

namespace Vis\Builder\Fields;

class MultiFile extends File
{
    public $onlyForm = true;

    public function getValueArray()
    {
        $value = $this->getValue();

        return json_decode($value);
    }
}
