<?php namespace Vis\Builder;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Vis\Builder\Models\Language as LanguageModel;

class SetLocale {

    public function __construct(Application $app, Request $request) {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->app->setLocale(LanguageModel::getDefaultLanguage()->language);

        return $next($request);
    }

}