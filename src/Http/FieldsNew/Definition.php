<?php

namespace Vis\Builder\FieldsNew;

use Illuminate\Support\Str;

class Definition extends Field
{
    protected $definitionRelation;
    protected $relation;
    protected $onlyForm = true;

    public function hasMany($relation, $classDefinitionRelation = null)
    {
        $this->relation = $relation;
        $this->definitionRelation = $classDefinitionRelation;

        return $this;
    }

    public function getDefinitionRelation($definition)
    {
        if ($this->definitionRelation) {
            return new $this->definitionRelation();
        }

        $model = $definition->model()->{$this->relation}()->getRelated();
        $fullPathClass = 'App\\Cms\\Definitions\\'. Str::plural(class_basename($model));

        return new $fullPathClass();
    }

    public function getAttributes($definition)
    {
        $definitionRelation = $this->getDefinitionRelation($definition);

        $attributes = [
            'name' => $this->getNameField(),
            'table' => $definitionRelation->model()->getTable(),
            'caption' => $definitionRelation->model()->getTable(),
            'definition' => $definitionRelation->getNameDefinition(),
            'definition_parent' => $definition->getNameDefinition(),
            'ident' => $this->getNameField(),
            'show' => [
                'id',
                'title'
            ]
        ];

        if ($definitionRelation->getIsSortable()) {
            $attributes['sortable'] = 'priority';
        }

        return json_encode($attributes);
    }

    public function getTable($definition, $parseJsonData)
    {
        $attributes = json_encode($parseJsonData);

        $model = $definition->model();

        $list = $model::find(request('id'))->{$this->relation};
        $fieldsDefinition = $this->head($definition);

        $list->map(function ($item, $key) use ($fieldsDefinition, $definition) {
            $item->fields = clone $fieldsDefinition;
            $fieldsDefinition->map(function ($item2, $key) use ($item, $definition) {
                $item->fields[$key] = clone $item2;
                $item2->setValue($item);
                $item->fields[$key]->value = $item2->getValueForList($definition);
            });
        });

        $urlAction = 'actions/' . $definition->model()->getTable();
        $isSortable = $this->getDefinitionRelation($definition)->getIsSortable();

        return [
            'html' => view('admin::new.form.fields.partials.input_definition_table_data',
                            compact('fieldsDefinition', 'list', 'attributes', 'urlAction', 'isSortable'))->render(),
            'count_records' => 0
        ];
    }

    public function remove($definition, $parseJsonData)
    {
        $this->getDefinitionRelation($definition)->model()->destroy(request('idDelete'));

        return $this->getTable($definition, $parseJsonData);
    }

    protected function head($definition)
    {
        $fields = $this->getDefinitionRelation($definition)->getAllFields();

        return collect($fields)->reject(function ($name) {
            return $name->onlyForm == true;
        });
    }

    public function getNameField() : string
    {
        return Str::slug(parent::getNameField());
    }

}
