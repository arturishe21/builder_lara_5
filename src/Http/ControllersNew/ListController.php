<?php

namespace Vis\Builder\ControllersNew;

use Vis\Builder\Services\Listing;

class ListController
{
    private $definition;

    public function __construct($definition)
    {
        $this->definition = $definition;
    }

    public function list()
    {
        $list = new Listing($this->definition);
        $listingRecords = $list->body();

        return view('admin::new.list.table', compact('list', 'listingRecords'));
    }
}
