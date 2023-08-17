<?php

namespace Vis\Builder\Settings;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;

class Login
{
    protected string $backgroundUrl = '/packages/vis/builder/img/vis-admin-lock.jpg';
    protected $css;

    public function onLogin(): RedirectResponse
    {
        return Redirect::to('/admin/tree');
    }

    public function onLogout(): RedirectResponse
    {
        return Redirect::to('/');
    }

    public function getBackground(): string
    {
        return $this->backgroundUrl;
    }

    public function getCss()
    {
        return $this->css;
    }
}
