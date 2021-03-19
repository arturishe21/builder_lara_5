<?php

namespace Vis\Builder\Setting;

abstract class AdminBase
{
    protected $caption = 'Административная часть сайта';
    protected $logoUrl = '/packages/vis/builder/img/logo-w.png';
    protected $faviconUrl = '/packages/vis/builder/img/favicon/favicon.ico';
    protected $css;
    protected $js;

    public function accessIp()
    {
        if (!setting('ip')) {
            return [];
        }

        return array_map('trim', explode(',', setting('ip')));
    }

    public function getCaption()
    {
        return __cms($this->caption);
    }

    public function getLogo()
    {
        return $this->logoUrl;
    }

    public function getFaviconUrl()
    {
        return $this->faviconUrl;
    }

    public function getCss()
    {
        if (is_array($this->css)) {
            return $this->css;
        }
    }

    public function getJs()
    {
        if (is_array($this->js)) {
            return $this->js;
        }
    }

    public function login()
    {
        return Login::class;
    }

    public function dashbord()
    {

    }

    abstract public function menu();
}
