<?php

namespace Vis\Builder;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Vis\Builder\Http\ViewComposers\ActivitiesTree;
use Vis\Builder\Http\ViewComposers\ChangeLang;
use Vis\Builder\Http\ViewComposers\Languages;
use Vis\Builder\Http\ViewComposers\LayoutDefault;
use Vis\Builder\Http\ViewComposers\Navigation;
use Vis\Builder\Http\ViewComposers\NavigationBadge;
use Vis\Builder\Models\TranslationsPhrases;
use Vis\Builder\Http\Middleware\Authenticate;
use Vis\Builder\Http\Middleware\AuthenticateFrontend;
use Vis\Builder\Http\Middleware\LocalizationMiddlewareRedirect;
use Vis\Builder\Console\GeneratePassword;
use Vis\Builder\Console\InstallCommand;
use Vis\Builder\Console\CreateConfig;
use Vis\Builder\Console\CreateImgWebp;

class BuilderServiceProvider extends ServiceProvider
{
    private string $commandAdminInstall = 'command.admin.install';
    private string $commandAdminGeneratePass = 'command.admin.generatePassword';
    private string $commandAdminCreateConfig = 'command.admin.createConfig';
    private string $commandAdminCreateImgWebp = 'command.admin.createImgWebp';

    public function boot(Router $router): void
    {
        include __DIR__.'/Http/helpers.php';

        $this->app->setLocale(defaultLanguage());

        $router->middleware('auth.admin', Authenticate::class);
        $router->middleware('auth.user', AuthenticateFrontend::class);

        $this->setupRoutes($this->app->router);

        $this->loadViewsFrom(realpath(__DIR__.'/resources/views'), 'admin');

        $this->publishes(
            [
            __DIR__
            .'/published/assets' => public_path('packages/vis/builder'),
            __DIR__.'/config'    => config_path('builder/'),
            ], 'builder'
        );

        $this->publishes(
            [
            __DIR__
            .'/published/assets' => public_path('packages/vis/builder'),
            ], 'public'
        );

        $this->publishes(
            [
            realpath(__DIR__.'/Migrations') => $this->app->databasePath().'/migrations',
            ]
        );

        $this->viewComposersInit();
    }

    private function viewComposersInit(): void
    {
        View::composer(
            [
            'admin::partials.change_lang',
            'admin::partials.scripts'
            ],
            ChangeLang::class
        );

        View::composer('admin::partials.navigation_badge', NavigationBadge::class);
        View::composer('admin::partials.navigation', Navigation::class);

        View::composer(
            ['admin::tree.partials.update',
            'admin::tree.partials.preview',
            'admin::tree.partials.clone',
            'admin::tree.partials.revisions',
            'admin::tree.partials.delete',
            'admin::tree.partials.constructor',
            ], ActivitiesTree::class
        );

        View::composer(
            [
            'admin::translations.part.form_trans',
            'admin::translations.part.result_search',
            'admin::translations.part.table_center',
            'admin::translations.trans'
            ], Languages::class
        );

        View::composer('admin::layouts.default',  LayoutDefault::class);
    }

    public function setupRoutes(Router $router): void
    {
        include __DIR__.'/Http/route_frontend.php';
        include __DIR__.'/Http/routers_translation_cms.php';
        include __DIR__.'/Http/routers.php';
        include __DIR__.'/Http/routers_translation.php';
    }

    public function register(): void
    {
        $this->app[\Illuminate\Contracts\Http\Kernel::class]->pushMiddleware(LocalizationMiddlewareRedirect::class);

        if (method_exists(Router::class, 'aliasMiddleware')) {
            $this->app[Router::class]->aliasMiddleware('auth.admin', Authenticate::class);
            $this->app[Router::class]->aliasMiddleware('auth.user', AuthenticateFrontend::class);
        }

        $this->registerCommands();
    }

    private function registerCommands(): void
    {
        $this->app->singleton(
            $this->commandAdminInstall, function () {
                return new InstallCommand();
            }
        );

        $this->app->singleton(
            $this->commandAdminGeneratePass, function () {
                return new GeneratePassword();
            }
        );

        $this->app->singleton(
            $this->commandAdminCreateConfig, function () {
                return new CreateConfig();
            }
        );

        $this->app->singleton(
            $this->commandAdminCreateImgWebp, function () {
                return new CreateImgWebp();
            }
        );

        $this->app->singleton(
            'arrayTranslate', function () {
                return TranslationsPhrases::fillCacheTrans();
            }
        );

        $this->commands($this->commandAdminInstall);
        $this->commands($this->commandAdminGeneratePass);
        $this->commands($this->commandAdminCreateConfig);
        $this->commands($this->commandAdminCreateImgWebp);
    }

    public function provides(): array
    {
        return [
            $this->commandAdminInstall,
            $this->commandAdminGeneratePass,
            $this->commandAdminCreateConfig,
            $this->commandAdminCreateImgWebp,
        ];
    }
}
