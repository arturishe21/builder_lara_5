<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Arr;

class SelectWithPicture extends Select
{
    public function getValueForList($definition)
    {
        $value = $this->getValue();
        $optionsRes = $this->getOptions();

        $options = [];
        foreach ($optionsRes as $key => $arrayValues) {
            if (is_array($arrayValues) && isset($arrayValues['value'])) {
                $options[$key] = $arrayValues['value'];
            }
        }

        if (isset($options[$value]) && Arr::get($options, $value)) {
            return $options[$value];
        }
    }

}
