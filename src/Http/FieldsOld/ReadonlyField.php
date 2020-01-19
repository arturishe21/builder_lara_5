<?php

namespace Vis\Builder\Fields;

/**
 * Class ReadonlyField.
 */
class ReadonlyField extends AbstractField
{
    /**
     * @param array $row
     *
     * @return string
     */
    public function getEditInput($row = [])
    {
        return e($this->getValue($row));
    }

    /**
     * @return bool
     */
    public function isReadonly()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEditable()
    {
        return false;
    }
}
