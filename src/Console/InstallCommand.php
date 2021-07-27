<?php

namespace Vis\Builder;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for install base version cms';

    private $installPath;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->installPath = __DIR__.'/../install';

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->confirm('Start install? [y|n]')) {
            $this->createDb();
            $this->loadFiles();
            $this->publishConfigs();
            $this->loadFilesAfterPublishConfigs();
            $this->deleteFiles();
            $this->finishInstall();
        }
    }

    /*
     * created database
     */
    private function createDb()
    {
        $this->info('start migration');

        \Artisan::call('migrate', [
            '--path' => 'vendor/vis/builder_lara_5/src/Migrations',
        ]);

        \Artisan::call('db:seed', [
            '--class' => 'Vis\\Builder\\AdminSeeder',
        ]);

        $this->insertTranslateData();

        $this->info('finish migration');
    }

    private function insertTranslateData()
    {
        DB::unprepared(file_get_contents($this->installPath.'/dump_sql_table/translations_phrases_cms.sql'));
        DB::unprepared(file_get_contents($this->installPath.'/dump_sql_table/translations_cms.sql'));
    }

    /*
     * load and replace basic files
     */
    private function loadFiles()
    {
        File::copyDirectory($this->installPath . '/files/app/Cms', app_path('Cms'));

        copy(
            $this->installPath.'/files/app/Providers/RouteServiceProvider.php',
            app_path().'/Providers/RouteServiceProvider.php'
        );
        $this->info('Created '.app_path().'/Providers/RouteServiceProvider.php - OK');

        copy(
            $this->installPath.'/files/app/Exceptions/Handler.php',
            app_path().'/Exceptions/Handler.php'
        );
        $this->info('Created '.app_path().'/Exceptions/Handler.php - OK');

        copy(
            $this->installPath.'/files/app/Providers/ComposerServiceProvider.php',
            app_path().'/Providers/ComposerServiceProvider.php'
        );
        $this->info('Created '.app_path().'/Providers/ComposerServiceProvider.php - OK');

        copy($this->installPath.'/files/routes/front.php', base_path().'/routes/front.php');
        $this->info('Created '.base_path().'/routes/front.php - OK');

        copy($this->installPath.'/files/routes/web.php', base_path().'/routes/web.php');
        $this->info('Created '.base_path().'/routes/web.php - OK');

        copy($this->installPath.'/files/public/.htaccess', public_path().'/.htaccess');
        $this->info('Replace htaccess - OK');

        copy($this->installPath.'/files/public/robots.txt', public_path().'/robots.txt');
        $this->info('Replace robots.txt - OK');

        if (! is_dir(app_path().'/Services')) {
            File::makeDirectory(app_path().'/Services', 0777, true);
            $this->info('Folder app/Services is Services');
        }

        if (! is_dir(app_path().'/Http/ViewComposers')) {
            File::makeDirectory(app_path().'/Http/ViewComposers', 0777, true);
            $this->info('Folder Http/ViewComposers is created');
        }

        copy($this->installPath.'/files/app/Http/ViewComposers/BreadcrumbsComposer.php', app_path().'/Http/ViewComposers/BreadcrumbsComposer.php');
        $this->info('Created app/Http/ViewComposers/BreadcrumbsComposer.php- OK');

        copy($this->installPath.'/files/app/Http/ViewComposers/FooterComposer.php', app_path().'/Http/ViewComposers/FooterComposer.php');
        $this->info('Created app/Http/ViewComposers/FooterComposer.php- OK');

        copy($this->installPath.'/files/app/Http/ViewComposers/HeaderComposer.php', app_path().'/Http/ViewComposers/HeaderComposer.php');
        $this->info('Created app/Http/ViewComposers/HeaderComposer.php- OK');

        copy($this->installPath.'/files/HomeController.php', app_path().'/Http/Controllers/HomeController.php');
        $this->info('Created app/Http/Controllers/HomeController.php- OK');

        copy($this->installPath.'/files/Breadcrumbs.php', app_path().'/Services/Breadcrumbs.php');
        $this->info('Created app/Services/Breadcrumbs.php- OK');

        \File::copyDirectory( $this->installPath . '/files/Models', app_path('Models'));

        if (! is_dir(base_path().'/resources/views/layouts')) {
            File::makeDirectory(base_path().'/resources/views/layouts', 0777, true);
            $this->info('Folder resources/views/layouts is created');
        }
        if (! is_dir(base_path().'/resources/views/home')) {
            File::makeDirectory(base_path().'/resources/views/home', 0777, true);
            $this->info('Folder resources/views/home is created');
        }
        if (! is_dir(base_path().'/resources/views/partials')) {
            File::makeDirectory(base_path().'/resources/views/partials', 0777, true);
            $this->info('Folder resources/views/partials is created');
        }
        if (! is_dir(base_path().'/resources/views/popups')) {
            File::makeDirectory(base_path().'/resources/views/popups', 0777, true);
            $this->info('Folder resources/views/popups is created');
        }
        if (! is_dir(base_path().'/resources/views/front')) {
            File::makeDirectory(base_path().'/resources/views/front', 0777, true);
            $this->info('Folder resources/views/front is created');
        }

        if (! is_dir(base_path().'/resources/lang/ru')) {
            File::makeDirectory(base_path().'/resources/lang/ru', 0777, true);
        }

        if (! is_dir(base_path().'/resources/lang/ua')) {
            File::makeDirectory(base_path().'/resources/lang/ua', 0777, true);
        }

        copy($this->installPath.'/files/seo.blade.php',
            base_path().'/resources/views/partials/seo.blade.php');

        copy(
            $this->installPath.'/files/resources/lang/ru/validation.php',
            base_path().'/resources/lang/ru/validation.php'
        );
        copy(
            $this->installPath.'/files/resources/lang/ua/validation.php',
            base_path().'/resources/lang/ua/validation.php'
        );

        copy(
            $this->installPath.'/files/resources/views/front/index.blade.php',
            base_path().'/resources/views/front/index.blade.php'
        );
        $this->info('Created front/index.blade.php- OK');

        copy($this->installPath.'/files/resources/views/layouts/default.blade.php', base_path().'/resources/views/layouts/default.blade.php');
        $this->info('Created default.blade.php- OK');

        copy($this->installPath.'/files/resources/views/home/index.blade.php', base_path().'/resources/views/home/index.blade.php');
        $this->info('Created index.blade.php- OK');

        exec('composer dump-autoload');
    }

    /*
     * published all configs file
     */
    private function publishConfigs()
    {
        $this->call('vendor:publish');
    }

    public function loadFilesAfterPublishConfigs()
    {
        copy($this->installPath.'/files/laravellocalization.php', config_path().'/laravellocalization.php');
        $this->info('Replace laravellocalization.php - OK');

        copy($this->installPath.'/files/imagecache.php', config_path().'/imagecache.php');
        $this->info('Replace imagecache.php - OK');

        copy($this->installPath.'/files/cartalyst.sentinel.php', config_path().'/cartalyst.sentinel.php');
        $this->info('Replace cartalyst.sentinel.php - OK');

        copy($this->installPath.'/files/debugbar.php', config_path().'/debugbar.php');
        $this->info('Replace debugbar.php - OK');

    }

    private function deleteFiles()
    {
        @unlink(app_path().'/User.php');

        File::deleteDirectory(app_path().'/Http/Controllers/Auth');
        @unlink(base_path().'/resources/views/welcome.blade.php');

        @unlink(base_path().'/database/migrations/2014_07_02_230147_migration_cartalyst_sentinel.php');
        @unlink(base_path().'/database/migrations/2014_10_12_000000_create_users_table.php');
    }

    /*
     * call cache:clear and other commands
     */
    private function finishInstall()
    {
        exec('composer dump-autoload');
        $this->info('composer dump-autoload completed');

        $this->call('cache:clear');
        $this->call('clear-compiled');
    }
}
