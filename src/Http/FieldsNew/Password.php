<?php

namespace Vis\Builder\FieldsNew;

class Password extends Field
{
    public function getValueForList($definition)
    {
        return '*****';
    }
}
