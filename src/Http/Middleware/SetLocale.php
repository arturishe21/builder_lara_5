<?php namespace Vis\Builder\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Vis\Builder\Models\Language as LanguageModel;

class SetLocale
{
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    public function handle(Request $request, Closure $next): mixed
    {
        $this->app->setLocale(LanguageModel::getDefaultLanguage()->language);

        return $next($request);
    }
}