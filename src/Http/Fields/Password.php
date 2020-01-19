<?php

namespace Vis\Builder\Fields;

class Password extends Field
{
    public function getValueForList($definition)
    {
        return '*****';
    }
}
