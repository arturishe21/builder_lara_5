<?php

namespace App\Cms\Filds;

use Vis\Builder\FieldsNew\Checkbox;

class Permissions extends Checkbox
{
    public function getFieldForm($definition)
    {
        $field = $this;

        return view('cms.fields.permissions', compact('definition', 'field'))->render();
    }
}
