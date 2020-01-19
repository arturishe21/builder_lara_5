<?php

namespace Vis\Builder\Fields;

class FileStorageField extends AbstractField
{
    public function isEditable()
    {
        return true;
    }

    public function getListValue($row)
    {
    }

    public function onSearchFilter(&$db, $value)
    {
    }

    public function getEditInput($row = [])
    {
        if ($this->hasCustomHandlerMethod('onGetEditInput')) {
            $res = $this->handler->onGetEditInput($this, $row);
            if ($res) {
                return $res;
            }
        }

        $input = view('admin::tb.storage.file.input');
        $input->value = $this->getValue($row);
        $input->row = $row;
        $input->name = $this->getFieldName();
        $input->caption = $this->getAttribute('caption');
        $input->placeholder = $this->getAttribute('placeholder');

        return $input->render();
    }

    public function prepareQueryValue($value)
    {
        return $value;
    }
}
