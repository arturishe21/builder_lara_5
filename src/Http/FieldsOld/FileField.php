<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\URL;

/**
 * Class FileField.
 */
class FileField extends AbstractField
{
    /**
     * @return bool
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * @param $db
     * @param $value
     */
    public function onSearchFilter(&$db, $value)
    {
        $db->where($this->getFieldName(), 'LIKE', '%'.$value.'%');
    }

    /**
     * @param array $row
     *
     * @throws \Throwable
     *
     * @return string
     */
    public function getEditInput($row = [])
    {
        if ($this->hasCustomHandlerMethod('onGetEditInput')) {
            $res = $this->handler->onGetEditInput($this, $row);
            if ($res) {
                return $res;
            }
        }

        $type = $this->getAttribute('type');

        $input = view('admin::tb.input_'.$type);
        $input->value = $this->getValue($row);
        $input->name = $this->getFieldName();
        $input->is_multiple = $this->getAttribute('is_multiple');
        $input->accept = $this->getAttribute('accept');
        $input->comment = $this->getAttribute('comment');
        $input->className = $this->getAttribute('class_name');
        $input->chooseFromUploaded = $this->getAttribute('choose_from_uploaded', true);

        if ($input->value && $this->isJson($input->value)) {
            $input->source = json_decode($input->value);
        }

        return $input->render();
    }

    public function getTabbedEditInput($row = [])
    {
        if ($this->hasCustomHandlerMethod('onGetTabbedEditInput')) {
            $res = $this->handler->onGetTabbedEditInput($this, $row);
            if ($res) {
                return $res;
            }
        }

        $type = $this->getAttribute('type');

        $input = view('admin::tb.tab_input_'.$type);
        $input->value = $this->getValue($row);
        $input->name = $this->getFieldName();
        $input->caption = $this->getAttribute('caption');
        $input->tabs = $this->getPreparedTabs($row);
        $input->is_multiple = $this->getAttribute('is_multiple');
        $input->accept = $this->getAttribute('accept');
        $input->comment = $this->getAttribute('comment');
        $input->className = $this->getAttribute('class_name');
        $input->chooseFromUploaded = $this->getAttribute('choose_from_uploaded', true);

        $this->getPreparedTabsMulti($input->tabs);

        return $input->render();
    }

    private function getPreparedTabsMulti(&$tabs)
    {
        if ($this->getAttribute('is_multiple')) {
            foreach ($tabs as $k => $tab) {
                if ($this->isJson($tab['value'])) {
                    $tabs[$k]['source'] = json_decode($tab['value']);
                }
            }
        }
    }

    /**
     * @param $string
     *
     * @return bool
     */
    private function isJson($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * @param $row
     *
     * @return bool|string
     */
    public function getListValue($row)
    {
        if ($this->hasCustomHandlerMethod('onGetListValue')) {
            $res = $this->handler->onGetListValue($this, $row);
            if ($res) {
                return $res;
            }
        }

        if (! $this->getValue($row)) {
            return '';
        }

        $src = URL::to($this->getValue($row));

        return '<a href="'.$src.'" target="_blank">'.$src.'</a>';
    }
}
