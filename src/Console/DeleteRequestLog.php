<?php

namespace Singsys\LQ\Console;

use Illuminate\Console\Command;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Crypt;
use Singsys\LQ\Models\RequestLog;

class DeleteRequestLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lq:delete-request-log';

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
        RequestLog::truncate();

        $this->info('To Delete all Request Log.');
    }
}
