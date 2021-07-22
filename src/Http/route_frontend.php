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
}
