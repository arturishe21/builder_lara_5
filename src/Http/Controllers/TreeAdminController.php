<?php

namespace Vis\Builder;

use Illuminate\Routing\Controller;
use Vis\Builder\ControllersNew\TreeController;
use App\Cms\Tree\Tree as CmsTree;
use Illuminate\Http\Request;

class TreeAdminController extends Controller
{
    private $tree;

    public function __construct(CmsTree $tree)
    {
        $this->tree = new TreeController($tree);
    }

    public function index()
    {
        return $this->tree->list();
    }

    public function handle()
    {
        return $this->tree->handle();
    }

    public function showAll(Request $request)
    {
        $tree = resolve($request->model)::with('children')->defaultOrder()->get()->toTree();
        $parentIDs = [];

        return view('admin::tree.tree', compact('tree', 'parentIDs'));
    }

}