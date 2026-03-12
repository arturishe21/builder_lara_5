<?php

namespace App\Cms_\Tree;

use App\Cms_\Tree\Templates\Contacts;
use App\Cms_\Tree\Templates\Node;
use Vis\Builder\Http\Definitions\BaseTree;

class Tree extends BaseTree
{
    public function templates(): array
    {
        return [
            'main' => Node::class,
            'contacts' => Contacts::class
        ];
    }
}
