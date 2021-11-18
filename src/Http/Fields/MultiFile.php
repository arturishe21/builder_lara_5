<?php

namespace Vis\Builder\Fields;

class MultiFile extends File
{
    public function getValueArray()
    {
        return json_decode($this->getValue());
    }

    public function getValueForList($definition)
    {
        $collections = json_decode($this->getValue());
        $result = [];

        if ($this->getValue() && count($collections)) {

            $result = array_map(function ($file) {
                return "<a href='{$file}'>". basename($file). "<a>";
            }, $collections);
        }

        return implode('<br>', $result);
    }

    public function getValueForExel($definition)
    {
        $collections = json_decode($this->getValue());
        $result = [];

        if ($this->getValue() && count($collections)) {

            $result = array_map(function ($file) {
                return request()->getSchemeAndHttpHost() . $file;
            }, $collections);
        }

        return implode(', ', $result);
    }
}


