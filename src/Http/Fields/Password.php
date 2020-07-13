<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\Hash;

class Password extends Field
{
    public function getValueForList($definition)
    {
        return '*****';
    }

    public function prepareSave($request)
    {
        $nameField = $this->getNameField();

        return Hash::make($request[$nameField]);
    }
}
