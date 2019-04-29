<?php

namespace Singsys\LQ\Console;

use Illuminate\Console\Command;
use Schema;
use DB;

class MakeSection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lq-make:section
                            {name : The name of the migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the command to create model, Model Policy, migration and seeder   ';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $section_name = trim($this->argument('name'));

        if($section_name) {

            $table_name = str_plural(snake_case(strtolower($section_name)));
            $model_name = "Models\\".$section_name;
            $policy_name = $section_name.'Policy';
            $migration_name = $section_name.'Table';
            $seeder_name = $section_name.'TableSeeder';

            $this->call("make:model", ['name'=> $model_name]);
            $this->call("make:policy", ['name'=> $policy_name,'--model'=> $model_name]);
            $this->call("make:migration", ['name'=> $migration_name, '--create'=> $table_name]);
            $this->call("make:seed", ['name'=> $seeder_name]);
            $this->info('Section has been created.');
        }else {

            $this->error('Section name is required.');
        }
    }
}
