<?php

namespace Vis\Builder\Fields;

/**
 * Class WysiwygField.
 */
class WysiwygField extends AbstractField
{
    /**
     * @return bool
     */
    public function isEditable()
    {
        return true;
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

        return mb_substr(strip_tags($this->getValue($row)), 0, 300).'...';
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

        $input = view('admin::tb.input_wysiwyg_redactor');
        $input->value = $this->getValue($row);
        $input->name = $this->getFieldName();
        $input->toolbar = $this->getAttribute('toolbar');
        $input->comment = $this->getAttribute('comment');
        $input->inlineStyles = $this->getAttribute('inlineStyles');
        $input->options = $this->getAttribute('options');

        $action = $this->getUrlAction();
        if (isset($this->definition['options']['action_url_tree'])) {
            $action = $this->definition['options']['action_url_tree'];
        }
        $input->action = $action;

        return $input->render();
    }

    /**
     * @param array $row
     *
     * @throws \Throwable
     *
     * @return string
     */
    public function getTabbedEditInput($row = [])
    {
        if ($this->hasCustomHandlerMethod('onGetTabbedEditInput')) {
            $res = $this->handler->onGetTabbedEditInput($this, $row);
            if ($res) {
                return $res;
            }
        }

        $tableName = $this->definition['db']['table'].'_wysiwyg';

        $input = view('admin::tb.tab_input_wysiwyg_redactor');
        $input->value = $this->getValue($row);
        $input->name = $this->getFieldName();
        $input->toolbar = $this->getAttribute('toolbar');
        $input->tabs = $this->getPreparedTabs($row);
        $input->caption = $this->getAttribute('caption');
        $input->inlineStyles = $this->getAttribute('inlineStyles');
        $input->options = $this->getAttribute('options');
        $input->comment = $this->getAttribute('comment');
        $input->className = $this->getAttribute('class_name');

        $action = $this->getUrlAction();
        if (isset($this->definition['options']['action_url_tree'])) {
            $action = $this->definition['options']['action_url_tree'];
        }
        $input->action = $action;
        $input->pre = $row ? $tableName.'e' : $tableName.'c';

        return $input->render();
    }

    /**
     * @param $db
     * @param $value
     */
    public function onSearchFilter(&$db, $value)
    {
        $table = $this->definition['db']['table'];
        $tabs = $this->getAttribute('tabs');
        if ($tabs) {
            $field = $table.'.'.$this->getFieldName();
            $db->where(function ($query) use ($field, $value, $tabs) {
                foreach ($tabs as $tab) {
                    $query->orWhere($field.$tab['postfix'], 'LIKE', '%'.$value.'%');
                }
            });
        } else {
            $db->where($table.'.'.$this->getFieldName(), 'LIKE', '%'.$value.'%');
        }
    }
}
