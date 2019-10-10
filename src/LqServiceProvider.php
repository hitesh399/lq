<?php

namespace Singsys\LQ;

use Response;
use Illuminate\Http\Request;
use Singsys\LQ\Macros\ModelMacros;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class LqServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(Request $request)
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\GetClient::class,
                Console\MakeSection::class,
                Console\CacheSiteConfig::class,
                Console\DeleteRequestLog::class,
            ]);

            $this->registerMigrations();
        }
        $this->registeredMacros($request);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->offerPublishing();

        $this->app->singleton('Lq\Response', function ($app) {
            return new Lib\LqResponse();
        });

        $this->app->singleton('site_config', function ($app) {
            return new Lib\SiteConfigLib();
        });

        $this->app->singleton('LqRequestLog', function ($app) {
            return new Lib\LqRequestLog();
        });

        $this->app->singleton('permission', function ($app) {
            return new Lib\PermissionRepository();
        });

        // Response::macro('store', function()
        // {
        //     app('LqRequestLog')->ok($this);
        //     return $this;
        // });
    }

    /**
     * Register LQ's migration files.
     */
    protected function registerMigrations()
    {
        if ($this->app->runningInConsole()) {
            if (Schema::hasTable('users')) {
                return $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
            }
        }
    }

    /**
     * Setup the resource publishing groups for Passport.
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/lq.php' => config_path('lq.php'),
            ], 'lq-config');
        }
    }

    protected function registeredMacros($request)
    {
        EloquentBuilder::macro('total', function () use ($request) {
            return (new ModelMacros($this, $request))->total();
        });
        EloquentBuilder::macro('getSql', function () use ($request) {
            return (new ModelMacros($this, $request))->getSql();
        });
        EloquentBuilder::macro('lqPaginate', function ($columns = ['*'], $fetch_total_for_all_page = false) use ($request) {
            return (new ModelMacros($this, $request))->lqPaginate($columns, $fetch_total_for_all_page);
        });
        EloquentBuilder::macro('lqUpdate', function (array $values) use ($request) {
            return (new ModelMacros($this, $request))->lqUpdate($values);
        });
    }
}
