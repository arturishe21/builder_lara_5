<?php

namespace App\Cms\Tree;

use App\Cms\Tree\Templates\Contacts;
use App\Cms\Tree\Templates\Node;
use Vis\Builder\Definitions\BaseTree;

class Tree extends BaseTree
{
    public function templates()
    {
        return [
            'node' => Node::class,
            'contacts2' => Contacts::class
        ];
    }
}
