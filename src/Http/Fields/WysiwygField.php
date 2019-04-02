<?php

namespace Vis\Builder\Fields;

use Illuminate\Database\Eloquent\Builder;

class WysiwygField extends AbstractField
{
    protected $defaultAttributes = [
        'toolbar' => 'fullscreen, bold, italic, underline, strikeThrough, subscript, superscript, fontFamily, fontSize, 
            color, emoticons, inlineStyle, paragraphStyle,  paragraphFormat, align, formatOL, formatUL, outdent, indent, 
            quote, insertHR, insertLink, insertImage, insertVideo, insertFile, insertTable, undo, redo, clearFormatting, selectAll, html'
    ];

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
        $input->comment = $this->getAttribute('comment', null);
        $input->inlineStyles = ($styles = $this->getAttribute('inlineStyles')) ? json_encode($styles) : '';
        $input->options = ($opt = $this->getAttribute('options')) ? json_encode($opt) : '';
        $input->action = $this->definition->getActionUrlTree() ?? $this->getUrlAction();

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

        $tableName = $this->definition->getTable().'_wysiwyg';

        $input = view('admin::tb.tab_input_wysiwyg_redactor');
        $input->value = $this->getValue($row);
        $input->name = $this->getFieldName();
        $input->toolbar = $this->getAttribute('toolbar');
        $input->tabs = $this->getPreparedTabs($row);
        $input->caption = $this->getAttribute('caption');
        $input->inlineStyles = ($styles = $this->getAttribute('inlineStyles')) ? json_encode($styles) : '';
        $input->options = ($opt = $this->getAttribute('options')) ? json_encode($opt) : '';
        $input->comment = $this->getAttribute('comment');
        $input->className = $this->getAttribute('class_name');
        $input->action = $this->definition->getActionUrlTree() ?? $this->getUrlAction();
        $input->pre = $row ? $tableName.'e' : $tableName.'c';

        return $input->render();
    }

    public function onSearchFilter(Builder $builder, $value)
    {
        $table = $this->definition->getTable();
        $tabs = $this->getAttribute('tabs');

        if ($tabs) {
            $field = $table.'.'.$this->getFieldName();
            $builder->where(function ($query) use ($field, $value, $tabs) {
                foreach ($tabs as $tab) {
                    $query->orWhere($field.$tab['postfix'], 'LIKE', '%'.$value.'%');
                }
            });
        } else {
            $builder->where($table.'.'.$this->getFieldName(), 'LIKE', '%'.$value.'%');
        }
    }

    public function toolbar(string $options)
    {
        $this->attributes['toolbar'] = $options;

        return $this;
    }

    public function options(array $options)
    {
        $this->attributes['options'] = $options;

        return $this;
    }
}
