<?php

namespace Vis\Builder;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

/**
 * Class BuilderServiceProvider.
 */
class BuilderServiceProvider extends ServiceProvider
{
    private $commandAdminInstall = 'command.admin.install';
    private $commandAdminGeneratePass = 'command.admin.generatePassword';
    private $commandAdminCreateConfig = 'command.admin.createConfig';
    private $commandAdminCreateImgWebp = 'command.admin.createImgWebp';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        $router->middleware('auth.admin', \Vis\Builder\Authenticate::class);
        $router->middleware('auth.user', \Vis\Builder\AuthenticateFrontend::class);

        require __DIR__.'/../vendor/autoload.php';
        require __DIR__.'/Http/helpers.php';
        require __DIR__.'/Http/view_composers.php';

        $this->setupRoutes($this->app->router);

        $this->loadViewsFrom(realpath(__DIR__.'/resources/views'), 'admin');

        $this->publishes([
            __DIR__
            .'/published/assets' => public_path('packages/vis/builder'),
            __DIR__.'/config'    => config_path('builder/'),
        ], 'builder');

        $this->publishes([
            __DIR__
            .'/published/assets' => public_path('packages/vis/builder'),
        ], 'public');

        $this->publishes([
            realpath(__DIR__.'/Migrations') => $this->app->databasePath().'/migrations',
        ]);
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        require __DIR__.'/Http/route_frontend.php';
        require __DIR__.'/Http/route_translation.php';
        require __DIR__.'/Http/route_settings.php';
        require __DIR__.'/Http/routers.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app[\Illuminate\Contracts\Http\Kernel::class]->pushMiddleware(LocalizationMiddlewareRedirect::class);

        if (method_exists(\Illuminate\Routing\Router::class, 'aliasMiddleware')) {
            $this->app[\Illuminate\Routing\Router::class]
                ->aliasMiddleware('auth.admin', \Vis\Builder\Authenticate::class);
            $this->app[\Illuminate\Routing\Router::class]
                ->aliasMiddleware('auth.user', \Vis\Builder\AuthenticateFrontend::class);
        }

        $this->registerCommands();
    }

    private function registerCommands()
    {
        $this->app->singleton($this->commandAdminInstall, function () {
            return new InstallCommand();
        });

        $this->app->singleton($this->commandAdminGeneratePass, function () {
            return new GeneratePassword();
        });

        $this->app->singleton($this->commandAdminCreateConfig, function () {
            return new CreateConfig();
        });

        $this->app->singleton($this->commandAdminCreateImgWebp, function () {
            return new CreateImgWebp();
        });

        $this->commands($this->commandAdminInstall);
        $this->commands($this->commandAdminGeneratePass);
        $this->commands($this->commandAdminCreateConfig);
        $this->commands($this->commandAdminCreateImgWebp);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            $this->commandAdminInstall,
            $this->commandAdminGeneratePass,
            $this->commandAdminCreateConfig,
            $this->commandAdminCreateImgWebp,
        ];
    }
}
