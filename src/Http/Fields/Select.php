<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Arr;

class Select extends Field
{
    private $options = [];
    private $isAction = false;

    public function options($arrayList)
    {
        $this->options = $arrayList;

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function action(bool $isAction = true)
    {
        $this->isAction = $isAction;

        return $this;
    }

    public function getAction() : bool
    {
        return $this->isAction;
    }

    public function getValueForList($definition)
    {
        $value = $this->getValue();
        $options = $this->getOptions();

        if (isset($options[$value]) && Arr::get($options, $value)) {
            return $options[$value];
        }
    }

}
