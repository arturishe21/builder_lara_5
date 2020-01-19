<?php

namespace App\Cms\Filds;

use Vis\Builder\Fields\Checkbox;

class Permissions extends Checkbox
{
    public function getFieldForm($definition)
    {
        $field = $this;

        return view('cms.fields.permissions', compact('definition', 'field'))->render();
    }
}
