<?php

namespace Vis\Builder\Fields;

class MultiFile extends File
{
    public $onlyForm = true;

    public function getValueArray()
    {
        return json_decode($this->getValue());
    }
}
