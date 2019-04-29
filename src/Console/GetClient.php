<?php

namespace Singsys\LQ\Console;

use Illuminate\Console\Command;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Crypt;

class GetClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lq:client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the commands to get the encrypted client id.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        //personalAccessClient
        $clients =Passport::client()->get();

        foreach($clients as $client) {

            $this->line($client->name . ': ' . Crypt::encryptString($client->id));
        }
    }
}
