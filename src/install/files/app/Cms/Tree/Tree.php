<?php

namespace App\Cms\Tree;

use App\Cms\Tree\Templates\Contacts;
use App\Cms\Tree\Templates\Node;
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
