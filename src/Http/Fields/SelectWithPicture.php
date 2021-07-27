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
            return $options[$value] . $this->getImg($value);
        }
    }

    public function getImg($value)
    {
        $optionsRes = $this->getOptions();

        if (isset($optionsRes[$value]) && Arr::get($optionsRes, $value) && isset($optionsRes[$value]['data-img'])) {

            $image = $optionsRes[$value]['data-img'];

            if (!$image) {
                return false;
            }

            $imageSmall = glide($image, ['w' => 100, 'h' => 100]);
            $imageHover = glide($image, ['w' => 550, 'h' => 550]);

            return "<a class='screenshot' style='margin-left:20px;' rel='{$imageHover}'><img style='max-width: 100px' src='{$imageSmall}'></a>";
        }
    }
}
