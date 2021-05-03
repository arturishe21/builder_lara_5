<?php

namespace Vis\Builder\Http\ViewComposers;

use Illuminate\View\View;

class LayoutDefault
{
    public function compose(View $view)
    {
        $admin = new \App\Cms\Admin();

        $skin = request()->cookie('skin') ?: 'smart-style-4';
        $customJs = config('builder.admin.custom_js');
        $customCss = config('builder.admin.custom_css');
        $logoWhite = config('builder.admin.logo_url_white') ?: '/packages/vis/builder/img/logo-w.png';

        if ($skin && $skin != 'smart-style-0') {
            $logo = $logoWhite;
        }

        $view->with(compact('skin',  'customJs', 'customCss', 'logo', 'admin'));
    }
}
