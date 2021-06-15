<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Str;
use Vis\Builder\Models\Language;

class Field
{
    protected $name;
    protected $attribute;
    protected $onlyForm = false;
    protected $fastEdit = false;
    public $value = '';
    protected $valueLanguage;
    protected $isSortable = false;
    protected $defaultValue;
    protected $placeholderValue;
    protected $rules = null;
    protected $nullValue;
    protected $language;
    protected $isManyToMany = false;
    protected $filter;
    protected $commentText = '';
    protected $relationHasOne;
    protected $relationMorphOne;
    protected $classNameField;
    protected $allData;
    protected $locale;
    protected $isReadonlyForEdit = false;

    public function __construct(string $name, $attribute = null)
    {
        $this->name = $name;
        $this->attribute = $attribute ?? str_replace(' ', '_', Str::lower($name));
        $this->locale = config('app.locale');
    }

    public function setValue($value)
    {
        $this->allData = $value;

        if ($this->getHasOne()) {
            $relation = $value->{$this->getHasOne()};

            if ($this->getLanguage()) {
                if ($relation) {
                    $this->valueLanguage = json_decode($relation->{$this->attribute});
                }

                return;
            }

            $this->value = $relation ? $relation->{$this->attribute} : '';

            return;
        }

        if ($this->getMorphOne()) {
            $relation = $value->{$this->getMorphOne()};

            if ($this->getLanguage()) {
                if ($relation) {
                    $this->valueLanguage = json_decode($relation->{$this->attribute});
                }

                return;
            }

            $this->value = $relation ? $relation->{$this->getNameField()} : $relation;

            return;
        }

        if ($this->getLanguage()) {
            $this->valueLanguage = json_decode($value[$this->attribute]);
        }

        $this->value = $value[$this->attribute];
    }

    public function className($class)
    {
        if (is_null($this->classNameField)) {
            $this->classNameField = $class;
        } else {
            $this->classNameField .= " $class";
        }

        return $this;
    }

    public function getClassName()
    {
        return $this->classNameField ? 'section_field '. $this->classNameField : '';
    }

    public function getId()
    {
        return isset($this->allData->id) ? $this->allData->id : '';
    }

    public function getValue()
    {
        return $this->value ? $this->value : $this->defaultValue;
    }

    public function isOnlyForm()
    {
        return $this->onlyForm;
    }

    public function isFilter()
    {
        return $this->filter;
    }

    public function getValueLanguage($postfix)
    {
        return $this->valueLanguage->$postfix ?? '';
    }

    public function getName()
    {
        return __cms($this->name);
    }

    public function getNameField()
    {
        if ($this->getHasOne()) {
            return $this->attribute . '_' . $this->getHasOne();
        }

        return $this->attribute;
    }

    public function getNameFieldLangTab($definition, $tab)
    {
        return $definition->getNameDefinition() . $this->getNameField() . $tab->language;
    }


    public function getNameFieldInBd()
    {
        return $this->attribute;
    }

    public function getValueForList($definition)
    {
        $arrayValue = json_decode($this->getValue());

        $value = $arrayValue->{$this->locale} ?? $this->getValue();

        if ($this->fastEdit) {

            $idRecord = $this->getId();
            $field = $this->getNameFieldInBd();

            return view('admin::list.fast_edit.field_base', compact('idRecord', 'value', 'field'));
        }

        return $value;
    }

    public function isOrder($list)
    {
        $order = session($list->getDefinition()->getSessionKeyOrder());

        return $order && $order['field'] == $this->getNameField() ? 'sorting_'.$order['direction'] : '';
    }

    public function getFilter($list)
    {
        $filter = session($list->getDefinition()->getSessionKeyFilter());

        return $filter && isset($filter['filter'][$this->getNameField()]) ?
            $filter['filter'][$this->getNameField()] : '';
    }

    public function isNull()
    {
        return false;
    }

    public function getReadonlyForEdit()
    {
        return $this->isReadonlyForEdit;
    }

    public function customUpdate()
    {
        return false;
    }

    public function filter($type = null)
    {
        $this->filter = $type ?:$this->getClassNameString();

        if (!view()->exists('admin::list.filters.'.$this->filter)) {
            $this->filter = 'text';
        }

        return $this;
    }

    public function getFilterInput($list)
    {
        if ($this->filter) {
            $field = $this;
            $filterValue = $this->getFilter($list);
            $definition = $list->getDefinition();

            return view('admin::list.filters.' . $this->filter, compact('field', 'filterValue', 'definition'));
        }
    }

    public function default($value)
    {
        $this->defaultValue = $value;

        return $this;
    }

    public function placeholder(string $value)
    {
        $this->placeholderValue = $value;

        return $this;
    }

    public function getPlaceholder() : ?string
    {
        return $this->placeholderValue;
    }

    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }

    public function sortable()
    {
        $this->isSortable = true;

        return $this;
    }

    public function isSortable()
    {
        return $this->isSortable;
    }

    public function onlyForm(bool $flag = true)
    {
        $this->onlyForm = $flag;

        return $this;
    }

    public function fastEdit(bool $flag = true)
    {
        $this->fastEdit = $flag;

        return $this;
    }

    public function language()
    {
        $this->language = (new Language())->getLanguages();

        return $this;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function getLanguageDefault()
    {
        return defaultLanguage();
    }

    public function rules($rules)
    {
        $this->rules = is_string($rules) ? (array)$rules : $rules;

        return $this;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function nullable($value)
    {
        $this->nullValue = $value;

        return $this;
    }

    public function isNullAble()
    {
        return (bool) $this->nullValue;
    }

    public function getNullValue()
    {
        return $this->nullValue;
    }

    public function isDisabled()
    {
        return false;
    }

    public function readonlyForEdit()
    {
        $this->isReadonlyForEdit = true;

        return $this;
    }

    public function comment(string $comment)
    {
        $this->commentText = $comment;

        return $this;
    }

    public function getComment() : string
    {
        return $this->commentText;
    }

    public function getFieldForm($definition)
    {
        $field = $this;
        $nameField = $this->getClassNameString();

        if ($this->getLanguage()) {
            $nameField .= '_lang';
        }

        return view('admin::form.fields.' . $nameField, compact('definition', 'field'))->render();
    }

    protected function getClassNameString() : string
    {
        return mb_strtolower(class_basename($this));
    }

    public function isManyToMany()
    {
        return $this->isManyToMany;
    }

    public function hasOne($relation)
    {
        $this->relationHasOne = $relation;

        return $this;
    }

    public function getHasOne()
    {
        return $this->relationHasOne;
    }

    public function morphOne($relation)
    {
        $this->relationMorphOne = $relation;

        return $this;
    }

    public function getMorphOne()
    {
        return $this->relationMorphOne;
    }

    public function prepareSave($request)
    {
        $nameField = $this->getNameField();

        return $request[$nameField];
    }

    public function fastSave($definition, $request)
    {
        $model = $definition->model()->find($request['pk']);
        $model->{$request['ident']} = $request['value'];
        $model->save();

        $definition->clearCache();
    }

}

