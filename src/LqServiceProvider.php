<?php

namespace Singsys\LQ;

use Illuminate\Http\Request;
use Singsys\LQ\Macros\ModelMacros;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class LqServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param \Illuminate\Http\Request $request [All request data]
     *
     * @return void|
     */
    public function boot(Request $request)
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    Console\InstallCommand::class,
                    Console\GetClient::class,
                    Console\MakeSection::class,
                    Console\CacheSiteConfig::class,
                    Console\DeleteRequestLog::class,
                    Console\GenerateMigration::class,
                ]
            );
        }
        $this->registeredMacros($request);
    }

    /**
     * Register the service provider.
     *
     * @return void|
     */
    public function register()
    {
        $this->offerPublishing();

        $this->app->singleton(
            'Lq\Response', function () {
                return new Lib\LqResponse();
            }
        );

        $this->app->singleton(
            'site_config',
            function () {
                return new Lib\SiteConfigLib();
            }
        );

        $this->app->singleton(
            'LqRequestLog', function () {
                return new Lib\LqRequestLog();
            }
        );

        $this->app->singleton(
            'permission', function () {
                return new Lib\PermissionRepository();
            }
        );
    }

    /**
     * Setup the resource publishing groups for Passport.
     *
     * @return void|
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__.'/../config/lq.php' => config_path('lq.php'),
                ],
                'lq-config'
            );
        }
    }

    /**
     * To Register the macro.
     *
     * @param \Illuminate\Http\Request $request [All request data]
     *
     * @return void|
     */
    protected function registeredMacros(Request $request)
    {
        EloquentBuilder::macro(
            'total', function () use ($request) {
                return (new ModelMacros($this, $request))->total();
            }
        );
        EloquentBuilder::macro(
            'getSql', function () use ($request) {
                return (new ModelMacros($this, $request))->getSql();
            }
        );
        EloquentBuilder::macro(
            'lqPaginate',
            function ($columns = ['*'], $fetch_total_for_all_page = false) use ($request) {
                return (new ModelMacros($this, $request))->lqPaginate(
                    $columns, $fetch_total_for_all_page
                );
            }
        );
        EloquentBuilder::macro(
            'lqUpdate', function (array $values) use ($request) {
                return (new ModelMacros($this, $request))->lqUpdate($values);
            }
        );
    }
}
