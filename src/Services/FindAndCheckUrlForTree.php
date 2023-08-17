<?php

namespace Vis\Builder\Services;

use App\Cms\Tree\Tree;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class FindAndCheckUrlForTree
{
    private string $model;

    public function getRoute($arrSegments)
    {
        $this->model = 'App\Models\Tree';

        $slug = $this->getSlug($arrSegments);

        $node = $this->findUrl($slug);

        if (! $node) {
            return false;
        }

        return $this->getControllerAndMethod($node);
    }

    private function getSlug($arrSegments): string
    {
        $slug = end($arrSegments);

        if (! $slug || $slug == LaravelLocalization::setLocale()) {
            $slug = '/';
        }

        return $slug;
    }

    private function findUrl($slug)
    {
        $tagsCache = ['tree'];
        $model = $this->model;

        if (request('show') == 1) {
            $nodes = $model::where('slug', 'like', $slug)->get();
        } else {
            $nodes = Cache::tags($tagsCache)->rememberForever('tree_slug_'.$slug, function () use ($model, $slug) {
                return $model::where('slug', 'like', $slug)->active()->get();
            });
        }

        foreach ($nodes as $node) {
            if (trim($node->getUrl(), '/') == Request::url()) {
                return $node;
            }
        }

        return false;
    }

    private function getControllerAndMethod($node): array|bool
    {
        $templates = (new Tree())->templates();

        if (! isset($templates[$node->template])) {
            return false;
        }

        $template =  new $templates[$node->template]();

        $controllerAndMethod = explode('@', $template->getAction());

        $app = app();
        $controller = $app->make('App\\Http\\Controllers\\'.$controllerAndMethod[0]);

        return [
            'controller' => $controller,
            'method' => $controllerAndMethod[1],
            'node' => $node,
        ];
    }
}
