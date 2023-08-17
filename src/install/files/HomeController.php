<?php

namespace App\Http\Controllers;

use Vis\Builder\Http\Controllers\TreeController;

class HomeController extends TreeController
{
    public function showPage()
    {
        $page = $this->node;

        return view('home.index', compact('page'));
    }
}
