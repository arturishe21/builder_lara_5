<?php

namespace Vis\Builder\Fields;

class Virtual extends Field
{
    public function setValue($value)
    {
        $this->value = 'ddd'.$value['email'];
    }
}
