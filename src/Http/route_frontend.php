<?php

$arrSegments = explode('/', Request::path());

if ($arrSegments[0] != 'admin') {
    try {

        $controllerMethodArray = (new \Vis\Builder\Services\FindAndCheckUrlForTree())->getRoute($arrSegments);

        if ($controllerMethodArray) {
            Route::group(
                ['middleware' => ['web']],
                function () use ($controllerMethodArray) {
                    Route::group(
                        ['prefix' => LaravelLocalization::setLocale()],
                        function () use ($controllerMethodArray) {
                            Route::get(
                                $controllerMethodArray['node']->getUrlNoLocation(),
                                function () use ($controllerMethodArray) {
                                    return $controllerMethodArray['controller']
                                        ->callAction('init', [$controllerMethodArray['node'], $controllerMethodArray['method']]);
                                }
                            );
                        }
                    );
                }
            );
        }
    } catch (Exception $e) {
    }

    /*
     * other tree
     */
    $otherTreeUrl = config('builder.tree.other_tree_url');

    if ($otherTreeUrl && is_array($otherTreeUrl)) {
        $startUrl = $arrSegments[0];

        if ($arrSegments[0] == LaravelLocalization::setLocale()) {
            if (isset($arrSegments[1])) {
                $startUrl = $arrSegments[1];
            } else {
                $startUrl = '/';
            }
        }

        $urls = array_keys($otherTreeUrl);

        if ($urls && count($urls) && in_array($startUrl, $urls)) {
            if (isset($otherTreeUrl[$startUrl])) {
                $configName = $otherTreeUrl[$startUrl];

                $definition = config('builder.'.$configName);
                $model = $definition['model'];

                $slug = end($arrSegments);

                if (! isset($slugTree)) {
                    $nodes = $model::where('slug', 'like', $slug)->get();

                    foreach ($nodes as $node) {
                        if ($node->getUrl() == Request::url()) {
                            break;
                        }
                    }

                    if (isset($node->id)) {
                        $_nodeUrl = $node->getUrlNoLocation();
                        $templates = $definition['templates'];

                        $middleware = ['web'];
                        if (isset($templates[$node->template]['middleware']) && ! empty($templates[$node->template]['middleware'])) {
                            foreach ((array) $templates[$node->template]['middleware'] as $midWare) {
                                $middleware[] = $midWare;
                            }
                        }

                        Route::group(
                            ['middleware' => $middleware],
                            function () use ($node, $_nodeUrl, $templates) {
                                Route::group(
                                    ['prefix' => LaravelLocalization::setLocale()],
                                    function () use ($node, $_nodeUrl, $templates) {
                                        Route::get(
                                            $_nodeUrl,
                                            function () use ($node, $templates) {
                                                if (! isset($templates[$node->template])) {
                                                    App::abort(404);
                                                }

                                                [$controller, $method] = explode('@', $templates[$node->template]['action']);

                                                $app = app();
                                                $controller = $app->make('App\\Http\\Controllers\\'.$controller);

                                                return $controller->callAction('init', [$node, $method]);
                                            }
                                        );
                                    }
                                );
                            }
                        );
                    }
                }
            }
        }
    }
}
