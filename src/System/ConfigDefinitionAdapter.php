<?php

namespace Vis\Builder\System;

use Illuminate\Database\Eloquent\Model;

class ConfigDefinitionAdapter extends AbstractDefinition
{
    protected $model;
    protected $caption;
    protected $fields;

    public function __construct(array $config)
    {
        if (isset($config['options']['model'])) {
            $this->model = new $config['options']['model'];
        }

        if (isset($config['options']['caption'])) {
            $this->caption = $config['options']['caption'];
        }

        if (isset($config['options']['fields'])) {
            $this->fields = $config['options']['fields'];
        }
    }

    public function getModel() : Model
    {
        return $this->model;
    }

    public function getCaption() : string
    {
        return $this->caption;
    }

    public function getFields() : array
    {
        return $this->fields;
    }
}
