<?php

namespace Singsys\LQ\Console;

use Illuminate\Console\Command;
use Schema;
use DB;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lq:install
                            {--force : Overwrite keys they already exist}
                            {--length=4096 : The length of the private key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the commands necessary to prepare Laravel Quick for use';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (Schema::hasTable('oauth_clients')) {
            DB::table('oauth_clients')->delete();
            $this->call('passport:keys', ['--force' => $this->option('force'), '--length' => $this->option('length')]);
            $this->call('passport:client', ['--password' => true, '--name' => 'Web.Admin']);
            $this->call('passport:client', ['--password' => true, '--name' => 'iOS']);
            $this->call('passport:client', ['--password' => true, '--name' => 'Android']);
            $this->call('passport:client', ['--password' => true, '--name' => 'Web.Front-End']);
        } else {
            $this->error('Laravel\Passport is required.');
        }
    }
}
