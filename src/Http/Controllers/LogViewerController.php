<?php

namespace Vis\Builder\Http\Controllers;

use Illuminate\Routing\Controller;
use Vis\Builder\Libs\LaravelLogViewer;

class LogViewerController extends Controller
{
    public function index(): mixed
    {
        $data = [
            'logs'         => LaravelLogViewer::all(),
            'files'        => LaravelLogViewer::getFiles(true),
            'current_file' => LaravelLogViewer::getFileName(),
        ];

        if (request()->wantsJson()) {
            return $data;
        }

        if (request()->ajax()) {
            return app('view')->make('admin::logs.index_ajax', $data);
        }

        return app('view')->make('admin::logs.index', $data);
    }
}
