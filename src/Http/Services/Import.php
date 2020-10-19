<?php

namespace Vis\Builder\Services;


class Import
{
    private $definition;

    public function __construct($definition)
    {
        $this->definition = (new $definition()) ;
    }

    public function show($list)
    {
        $nameDefinition = mb_strtolower(class_basename($this->definition));

        return view('admin::new.list.buttons.import', compact('list', 'nameDefinition'));
    }
}