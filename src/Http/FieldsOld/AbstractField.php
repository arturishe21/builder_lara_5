<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

abstract class AbstractField
{
    protected $fieldName;
    protected $attributes;
    protected $options;
    protected $definition;
    protected $handler;

    public function __construct($fieldName, $attributes, $options, $definition, $handler)
    {
        $this->attributes = $this->prepareAttributes($attributes);
        $this->options = $options;
        $this->definition = $definition;
        $this->fieldName = $fieldName;

        $this->handler = &$handler;
    }

    public function isPattern()
    {
        return false;
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function getUrlAction()
    {
        return '/admin/handle/'.$this->options['def_name'];
    }

    private function prepareAttributes($attributes)
    {
        $attributes['fast-edit'] = isset($attributes['fast-edit']) && $attributes['fast-edit'];
        $attributes['filter'] = isset($attributes['filter']) ? $attributes['filter'] : false;
        $attributes['hide'] = isset($attributes['hide']) ? $attributes['hide'] : false;
        $attributes['is_null'] = isset($attributes['is_null']) ? $attributes['is_null'] : false;

        return $attributes;
    }

    protected function getOption($ident)
    {
        return $this->options[$ident];
    }

    public function getAttribute($ident, $default = false)
    {
        if (isset($this->attributes[$ident])) {
            return $this->attributes[$ident];
        }

        return $default;
    }

    public function getAttributeCallable($ident, $default = false)
    {
        if (isset($this->attributes[$ident])) {
            if (is_callable($this->attributes[$ident])) {
                return $this->attributes[$ident]();
            }

            return $this->attributes[$ident];
        }

        return $default;
    }

    public function getRequiredAttribute($ident)
    {
        if (! array_key_exists($ident, $this->attributes)) {
            throw new \RuntimeException('Image storage field requires ['.$ident.'] attribute');
        }

        return $this->attributes[$ident];
    }

    public function isHidden()
    {
        return $this->getAttribute('hide');
    }

    public function getValue($row, $postfix = '')
    {
        if ($this->hasCustomHandlerMethod('onGetValue')) {
            $res = $this->handler->onGetValue($this, $row, $postfix);
            if ($res) {
                return $res;
            }
        }

        $fieldName = $this->getFieldName().$postfix;
        // postfix used for getting values for form - tabs loop
        // so there is no need to force appending postfix
        if ($this->getAttribute('tabs') && ! $postfix) {
            $tabs = $this->getAttribute('tabs');
            $fieldName = $fieldName.$tabs[0]['postfix'];
        }

        if (isset($row[$fieldName])) {
            return $row[$fieldName];
        } else {
            if ($this->getAttribute('default')) {
                return $this->getAttribute('default');
            }
        }
    }

    public function getExportValue($type, $row, $postfix = '')
    {
        if ($this->hasCustomHandlerMethod('onGetExportValue')) {
            $res = $this->handler->onGetExportValue($this, $type, $row, $postfix);
            if ($res) {
                return $res;
            }
        }

        $value = $this->getValue($row, $postfix);
        // cuz double quotes is escaping by more double quotes in csv
        $escapedValue = preg_replace('~"~', '""', $value);

        return $escapedValue;
    }

    public function getListValue($row)
    {
        if ($this->hasCustomHandlerMethod('onGetListValue')) {
            $res = $this->handler->onGetListValue($this, $row);
            if ($res) {
                return $res;
            }
        }

        return $this->getValue($row);
    }

    public function getListValueFastEdit($row, $ident)
    {
        $field = $this;
        $def = $this->definition;

        return view('admin::tb.fast_edit_generally', compact('row', 'ident', 'field', 'def'));
    }

    public function getReplaceStr($row)
    {
        if ($this->getAttribute('result_show')) {
            $arrParam = explode('%', $this->getAttribute('result_show'));

            foreach ($arrParam as $k => $val) {
                if (isset($row[$val])) {
                    $arrParam[$k] = $row[$val];
                }
            }

            return implode('', $arrParam);
        }
    }

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
        $input->rows = $this->getAttribute('rows');
        $input->mask = $this->getAttribute('mask');
        $input->placeholder = $this->getAttribute('placeholder');
        $input->comment = $this->getAttribute('comment');
        $input->disabled = $this->getAttribute('disabled');

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
        $tableName = $this->definition['db']['table'];

        $input = view('admin::tb.tab_input_'.$type);
        $input->value = $this->getValue($row);
        $input->name = $this->getFieldName();
        $input->rows = $this->getAttribute('rows');
        $input->mask = $this->getAttribute('mask');
        $input->custom_type = $this->getAttribute('custom_type');
        $input->placeholder = $this->getAttribute('placeholder');
        $input->caption = $this->getAttribute('caption');
        $input->tabs = $this->getPreparedTabs($row);
        $input->pre = $row ? $tableName.'_e' : $tableName.'_c';
        $input->comment = $this->getAttribute('comment');
        $input->className = $this->getAttribute('class_name');

        return $input->render();
    }

    protected function getPreparedTabs($row)
    {
        $tabs = $this->getAttribute('tabs');
        $required = [
            'placeholder',
            'postfix',
        ];
        foreach ($tabs as &$tab) {
            foreach ($required as $option) {
                if (! isset($tab[$option])) {
                    $tab[$option] = '';
                }
            }

            $tab['value'] = $this->getValue($row, $tab['postfix']);

            if (! $tab['value'] && isset($tab['default'])) {
                $tab['value'] = $tab['default'];
            }
        }

        return $tabs;
    }

    public function getFilterInput()
    {
        if (! $this->getAttribute('filter')) {
            return '';
        }

        $definitionName = $this->getOption('def_name');
        $sessionPath = 'table_builder.'.$definitionName.'.filters.'.$this->getFieldName();
        $filter = Session::get($sessionPath, '');

        $type = $this->getAttribute('filter');

        $input = view('admin::tb.filter_'.$type);
        $input->name = $this->getFieldName();
        $input->value = $filter;

        return $input->render();
    }

    protected function hasCustomHandlerMethod($methodName)
    {
        return $this->handler && is_callable([$this->handler, $methodName]);
    }

    public function prepareQueryValue($value)
    {
        if (! $value && $this->getAttribute('is_null')) {
            return;
        }

        if (is_null($value)) {
            return '';
        }

        return $value;
    }

    public function onSelectValue(&$db)
    {
        if ($this->hasCustomHandlerMethod('onAddSelectField')) {
            $res = $this->handler->onAddSelectField($this, $db);
            if ($res) {
                return $res;
            }
        }

        $tabs = $this->getAttribute('tabs');

        $tableName = $this->getAttribute('extends_table', $this->definition['db']['table']);
        $fieldName = $this->getFieldName();

        if ($tabs) {
            foreach ($tabs as $tab) {
                $name = $tableName.'.'.$this->getFieldName().$tab['postfix'];
                $db->addSelect($name);
            }
        } else {
            $db->addSelect($tableName.'.'.$fieldName);
        }
    }

    public function isReadonly()
    {
        return false;
    }

    public function getClientsideValidatorRules()
    {
        $validation = $this->getAttribute('validation');

        if (! isset($validation['client'])) {
            return;
        }

        $validation = $validation['client'];

        $rules = isset($validation['rules']) ? $validation['rules'] : [];
        $name = $this->getFieldName();
        $tabs = $this->getAttribute('tabs');

        $data = compact('rules', 'name', 'tabs');

        return view('admin::tb.validator_rules', $data)->render();
    }

    public function getClientsideValidatorMessages()
    {
        $validation = $this->getAttribute('validation');

        if (! isset($validation['client'])) {
            return;
        }

        $validation = $validation['client'];

        $messages = isset($validation['messages']) ? $validation['messages'] : [];
        $name = $this->getFieldName();
        $tabs = $this->getAttribute('tabs');

        $data = compact('messages', 'name', 'tabs');

        return view('admin::tb.validator_messages', $data)->render();
    }

    public function doValidate($value)
    {
        $validation = $this->getAttribute('validation');

        if (! isset($validation['server'])) {
            return;
        }

        $rules = $validation['server']['rules'];
        $messages = isset($validation['server']['messages']) ? $validation['server']['messages'] : [];
        $name = $this->getFieldName();

        if (isset($validation['server']['ignore_this_id']) && $validation['server']['ignore_this_id']) {
            if (class_exists('Illuminate\Validation\Rule')) {
                $rules = explode('|', $rules);
                $rules[] = Rule::unique($this->definition['db']['table'])->ignore(request('id'));
            } else {
                $rules .= ','.request('id');
            }
        }

        $validator = Validator::make(
            [
                $name => $value,
            ],
            [
                $name => $rules,
            ],
            $messages
        );

        if ($validator->fails()) {
            $errors = implode('|', $validator->messages()->all());

            throw new \Exception($errors);
        }
    }

    public function getSubActions()
    {
        return '';
    }

    public function getLabelClass()
    {
        return 'input';
    }

    public function isEditable()
    {
        return true;
    }

    public function getRowColor($row)
    {
        return '';
    }

    public function onSearchFilter(&$db, $value)
    {
        $table = $this->definition['db']['table'];

        if ($this->getAttribute('filter') == 'integer') {
            $db->where($table.'.'.$this->getFieldName(), $value);

            return;
        }

        if ($this->getAttribute('filter') == 'date_range') {
            if (! isset($value['to'])) {
                $db->where($table.'.'.$this->getFieldName(), '>', $value['from']);

                return;
            }

            if (! isset($value['from'])) {
                $db->where($table.'.'.$this->getFieldName(), '<', $value['to']);

                return;
            }

            $db->whereBetween($table.'.'.$this->getFieldName(), [$value['from'], $value['to']]);

            return;
        }

        $db->where($table.'.'.$this->getFieldName(), 'LIKE', '%'.$value.'%');
    }

    /*
        public function onSearchFilterDate(&$db, $value)
        {
            $table = $this->definition['db']['table'];

            if ($this->getAttribute('filter') == 'date_range') {

                if (!isset($value['to'])) {
                    $db->where($table.'.'.$this->getFieldName(), '>' , $value['from']);
                    return;
                }

                if (!isset($value['from'])) {
                    $db->where($table.'.'.$this->getFieldName(), '<' , $value['to']);
                    return;
                }

                $db->whereBetween($table.'.'.$this->getFieldName(), [$value['from'], $value['to']]);

                return;
            }
        }*/

    public function getListValueDefinitionPopup($row)
    {
        return strip_tags($this->getListValue($row), '<a><span><img><br>');
    }

    public function getWidth()
    {
        return $this->getAttribute('width') ? 'style="width:'.$this->getAttribute('width').'"' : '';
    }

    public function isOrder($controller)
    {
        $order = $controller->getOrderDefinition();

        return $order && $order['field'] == $this->getFieldName() ? 'sorting_'.$order['direction'] : '';
    }
}
