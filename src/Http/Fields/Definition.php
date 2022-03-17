<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Str;

class Definition extends Field
{
    protected $definitionRelation;
    protected $relation;
    protected $onlyForm = true;
    protected $typeRelative;

    public function hasMany($relation, $classDefinitionRelation = null)
    {
        $this->relation = $relation;
        $this->definitionRelation = $classDefinitionRelation;
        $this->typeRelative = 'hasMany';

        return $this;
    }

    public function morphMany($relation, $classDefinitionRelation = null)
    {
        $this->relation = $relation;
        $this->definitionRelation = $classDefinitionRelation;
        $this->typeRelative = 'morphMany';

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
            'foreign_field' => $this->getFieldForeignKeyName($definition),
            'path_definition' => addslashes($this->definitionRelation),
            'model_parent' => addslashes($definition->getFullPathDefinition()),
            'type_relation' => $this->typeRelative,
        ];

        if ($definitionRelation->getIsSortable()) {
            $attributes['sortable'] = 'priority';
        }

        if ($this->typeRelative == 'morphMany') {
            $attributes['morph_type'] = $definition->model()->{$this->relation}()->getMorphType();
            $attributes['model_base'] = addslashes($definition->model);
        }

        return json_encode($attributes);
    }

    private function getFieldForeignKeyName($definition)
    {
        return $definition->model()->{$this->relation}()->getForeignKeyName();
    }

    public function getTable($definition, $parseJsonData)
    {
        $attributes = json_encode($parseJsonData);
        $definitionRelation = $this->getDefinitionRelation($definition);
        $perPage = $definitionRelation->getPerPage();

        if (request('count')) {

            session()->put($definitionRelation->getSessionKeyPerPage(), ['per_page' => request('count')]);
        }

        $count = $definitionRelation->getPerPageThis();

       // dd($definitionRelation->getSessionKeyPerPage());

        $model = $definition->model();

        $listModel = request('id') ? $model::find(request('id')) : (new $model());

        $list = $listModel->{$this->relation}()->paginate($count);
        $list->appends(['count' => $count]);
        
        $fieldsDefinition = $this->head($definition);

        $list->map(function ($item, $key) use ($fieldsDefinition, $definition) {
            $item->fields = clone $fieldsDefinition;
            $fieldsDefinition->map(function ($item2, $key) use ($item, $definition) {
                $item->fields[$key] = clone $item2;
                $item2->setValue($item);
                $item->fields[$key]->value = $item2->getValueForList($definition);
            });
        });

        $urlAction = 'actions/'. $definition->getNameDefinition();
        $isSortable = $this->getDefinitionRelation($definition)->getIsSortable();


        return [
            'html' => view('admin::form.fields.partials.input_definition_table_data',
                            compact('definitionRelation', 'fieldsDefinition', 'list', 'attributes', 'urlAction', 'isSortable', 'perPage', 'count'))->render(),
            'count_records' => 0
        ];
    }

    public function remove($definition, $parseJsonData)
    {
        $this->getDefinitionRelation($definition)->model()->destroy(request('idDelete'));

        $this->getDefinitionRelation($definition)->clearCache();

        return $this->getTable($definition, $parseJsonData);
    }

    protected function head($definition)
    {
        $fields = $this->getDefinitionRelation($definition)->getAllFields();

        return collect($fields)->reject(function ($name) {
            return $name->isOnlyForm();
        });
    }

    public function getNameField() : string
    {
        return Str::slug(parent::getNameField());
    }

}
