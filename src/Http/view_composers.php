<?php

use Illuminate\View\View as ViewParam;

View::composer(['admin::layouts.default'], function (ViewParam $view) {

    $admin = new \App\Cms\Admin();

    $skin = Cookie::get('skin') ?: 'smart-style-4';
    $customJs = config('builder.admin.custom_js');
    $customCss = config('builder.admin.custom_css');
    $logoWhite = config('builder.admin.logo_url_white') ?: '/packages/vis/builder/img/logo-w.png';

    if ($skin && $skin != 'smart-style-0') {
        $logo = $logoWhite;
    }

    $view->with(compact('skin',  'customJs', 'customCss', 'logo', 'admin'));
});

View::composer('admin::partials.navigation', function (ViewParam $view) {
    $user = Sentinel::getUser();
    $menu =  (new \App\Cms\Admin())->menu();

    $view->with(compact('user', 'menu'));
});


View::composer('admin::partials.navigation_badge', function (ViewParam $view) {

    if(isset($view->menu['badge'])) {

        $badgeValue = $view->menu['badge']();

        $view->with(compact('badgeValue'));
    }
});

View::composer(['admin::tree.partials.update',
                     'admin::tree.partials.preview',
                     'admin::tree.partials.clone',
                     'admin::tree.partials.revisions',
                     'admin::tree.partials.delete',
                     'admin::tree.partials.constructor',
], function (ViewParam $view) {
    $type = $view->getData()['type'];
    $active = true;
    $caption = '';

    $view->with(compact('active', 'caption'));
});
