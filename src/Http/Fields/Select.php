<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Arr;

class Select extends Field
{
    private $options = [];
    private $isAction = false;
    private $actionSelect = false;

    public function options($arrayList)
    {
        $this->options = $arrayList;

        return $this;
    }

    public function optionsWithAttributes($arrayList)
    {
        foreach ($arrayList as $key => $arrayValues) {
            if (is_array($arrayValues) && isset($arrayValues['value'])) {
                $this->options[$key] = $arrayValues['value'];
                unset($arrayList[$key]['value']);
            }
        }

        $this->attributes = $arrayList;

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function action(bool $isAction = true)
    {
        $this->isAction = $isAction;

        return $this;
    }

    public function actionSelect($nameSelect)
    {
        $this->actionSelect = $nameSelect;

        return $this;
    }

    public function getActionSelect()
    {
        return $this->actionSelect;
    }

    public function getAction() : bool
    {
        return $this->isAction;
    }

    public function getValueForList($definition)
    {
        $value = $this->getValue();
        $optionsArray = $this->getOptions();

        if (isset($optionsArray[$value]) && Arr::get($optionsArray, $value)) {

            if ($this->fastEdit) {

                $idRecord = $this->getId();
                $field = $this->getNameFieldInBd();

                return view('admin::list.fast_edit.select', compact('idRecord', 'value', 'field', 'optionsArray'));
            }

            return $optionsArray[$value];
        }
    }

    public function getValueForExel($definition)
    {
        $value = $this->getValue();
        $optionsArray = $this->getOptions();

        if (isset($optionsArray[$value]) && Arr::get($optionsArray, $value)) {
            return $optionsArray[$value];
        }
    }

}
