<?php

namespace Singsys\LQ\Console;

use Illuminate\Console\Command;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Crypt;
use Cache;

class CacheSiteConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site_config:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the commands to cached all configuration.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $model = config('lq.site_config_class');
        $configs = $model::get(['name']);
        foreach ($configs as $config) {
            Cache::forget('site_config.'.$config->name);
            app('site_config')->get($config->name);
        }

        $this->info('Site configurations have been stored in cache.');
    }
}
