<?php

namespace Vis\Builder\Http\Services;

use Illuminate\Support\Collection;
use Vis\Builder\Http\Definitions\Resource;
use Illuminate\Pagination\LengthAwarePaginator;

class Listing
{
    private Resource $definition;

    public function __construct(Resource $definition)
    {
        $this->definition = $definition;
    }

    public function actions(): Actions
    {
        return new Actions($this->definition);
    }

    public function getDefinition(): Resource
    {
        return $this->definition;
    }

    public function title(): string
    {
        return $this->definition->getTitle();
    }

    public function getUrlAction(): string
    {
        return '/admin/actions/' . $this->getThisUrl();
    }

    public function getThisUrl(): string
    {
        $arraySlugs = explode('/', request()->url());

        return last($arraySlugs);
    }

    public function isSortable(): bool
    {
        return $this->definition->getIsSortable();
    }

    public function isMultiActions(): bool
    {
        return false;
    }

    public function head(): Collection
    {
        return $this->definition->head();
    }

    public function isFilterable(): Collection
    {
        $fields = $this->definition->head();

        return collect($fields)->reject(function ($name) {
            return $name->isFilter() == true;
        });
    }

    public function isShowInsert(): bool
    {
        return in_array('insert', $this->definition->actions()->getActionsAccess());
    }

    public function isShowAmount(): bool
    {
        $perPage = $this->definition->getPerPage();

        return is_array($perPage) && count($perPage);
    }

    public function getPerPage(): array
    {
        return $this->definition->getPerPage();
    }

    public function body(): Collection|LengthAwarePaginator
    {
        return $this->definition->getListing();
    }
}
