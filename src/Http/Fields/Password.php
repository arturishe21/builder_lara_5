<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\Hash;

class Password extends Field
{
    private $defaultPassword = '******';

    public function getValueForList($definition)
    {
        return $this->defaultPassword;
    }

    public function setValue($value)
    {
        if ($value) {
            $this->value = $this->defaultPassword;
        }
    }

    public function prepareSave($request)
    {
        $nameField = $this->getNameField();

        return Hash::make($request[$nameField]);
    }
}
