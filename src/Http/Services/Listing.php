<?php

namespace Vis\Builder\Services;

class Listing
{
    private $definition;

    public function __construct($definition)
    {
        $this->definition = $definition;
    }

    public function actions()
    {
        return new Actions($this->definition);
    }

    public function getDefinition()
    {
        return $this->definition;
    }

    public function title()
    {
        return $this->definition->getTitle();
    }

    public function getUrlAction()
    {
        $page = $this->definition->getNameDefinition();

        return '/admin/actions/' . $page;
    }

    public function isSortable()
    {
        return $this->definition->getIsSortable();
    }

    public function isMultiActions()
    {
        return false;
    }

    public function head()
    {
        return $this->definition->head();
    }

    public function isFilterable()
    {
        $fields = $this->definition->head();

        return collect($fields)->reject(function ($name) {
            return $name->isFilter() == true;
        });
    }

    public function isShowInsert()
    {
        return in_array('insert', $this->definition->actions()->getActionsAccess());
    }

    public function isShowAmount()
    {
        return is_array($this->definition->getPerPage());
    }

    public function getPerPage()
    {
        return $this->definition->getPerPage();
    }

    public function body()
    {
        return $this->definition->getListing();
    }

}
