<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\View;

class ImageStorageField extends AbstractField
{
    public function isEditable()
    {
        return true;
    }

    // end isEditable

    public function getListValue($row)
    {
    }

    // end getListValue

    public function onSearchFilter(&$db, $value)
    {
    }

    // end onSearchFilter

    public function getEditInput($row = [])
    {
        if ($this->hasCustomHandlerMethod('onGetEditInput')) {
            $res = $this->handler->onGetEditInput($this, $row);
            if ($res) {
                return $res;
            }
        }

        $input = view('admin::tb.storage.image.input');
        $input->value = $this->getValue($row);
        $input->row = $row;
        $input->name = $this->getFieldName();
        $input->caption = $this->getAttribute('caption');
        $input->placeholder = $this->getAttribute('placeholder');
        $input->type = $this->getRequiredAttribute('storage_type');

        if ($row) {
            $model = '\\'.\config('builder::images.models.image');
            $input->entity = $model::find($this->getValue($row));
        }

        return $input->render();
    }

    // end getEditInput

    public function prepareQueryValue($value)
    {
        return (! $value && $this->getAttribute('is_null')) ? null : $value;
    }

    // end prepareQueryValue
}
