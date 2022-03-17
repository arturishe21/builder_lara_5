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
        return '/admin/actions/' . $this->getThisUrl();
    }

    public function getThisUrl()
    {
        $arraySlugs = explode('/', request()->url());

        return last($arraySlugs);
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
        $perPage = $this->definition->getPerPage();

        return is_array($perPage) && count($perPage);
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
