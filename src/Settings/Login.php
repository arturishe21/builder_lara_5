<?php

namespace Vis\Builder\Setting;

use Illuminate\Support\Facades\Redirect;

class Login
{
    protected $backgroundUrl = '/packages/vis/builder/img/vis-admin-lock.jpg';
    protected $css;

    public function onLogin()
    {
        return Redirect::to('/admin/tree');
    }

    public function onLogout()
    {
        return Redirect::to('/');
    }

    public function getBackground()
    {
        return $this->backgroundUrl;
    }

    public function getCss()
    {
        return $this->css;
    }
}
