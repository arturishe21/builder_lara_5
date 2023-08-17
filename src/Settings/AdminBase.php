<?php

namespace Vis\Builder\Settings;

abstract class AdminBase
{
    protected string $caption = 'Административная часть сайта';
    protected string $logoUrl = '/packages/vis/builder/img/logo-w.png';
    protected string $faviconUrl = '/packages/vis/builder/img/favicon/favicon.ico';
    protected array $css = [];
    protected array $js = [];

    public function accessIp(): array
    {
        if (!setting('ip')) {
            return [];
        }

        return array_map('trim', explode(',', setting('ip')));
    }

    public function getCaption(): string
    {
        return __cms($this->caption);
    }

    public function getLogo(): string
    {
        return $this->logoUrl;
    }

    public function getFaviconUrl(): string
    {
        return $this->faviconUrl;
    }

    public function getCss(): array
    {
        if (is_array($this->css)) {
            return $this->css;
        }
    }

    public function getJs(): array
    {
        if (is_array($this->js)) {
            return $this->js;
        }
    }

    public function login(): string
    {
        return Login::class;
    }

    public function dashbord()
    {

    }

    abstract public function menu(): array;
}
