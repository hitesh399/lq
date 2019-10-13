<?php

namespace Singsys\LQ\Console;

use Illuminate\Console\Command;

class GenerateMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lq-make:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Generate Lq migration file.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!class_exists(\Laravel\Passport\Passport::class)) {
            $this->error('passport is mising. composer require laravel/passport');

            return;
        }
        $files = scandir(__DIR__.'/../../database/migrations', SCANDIR_SORT_ASCENDING);
        // dd($files);
        foreach ($files as $key => $file) {
            $file_name_arr = explode('.', $file);
            $ext = end($file_name_arr);
            if ($ext === 'php') {
                $file_name = preg_replace('/^[0-9]+[0-9\_]+/', '', $file);
                if (!$this->_isMigrationAlreadyExists($file_name)) {
                    copy(
                        __DIR__.'/../../database/migrations/'.$file,
                        base_path('database/migrations/'.date('Y_m_d_His').str_pad($key, 2, '0', STR_PAD_LEFT).'_'.$file_name)
                    );
                } else {
                    $this->info('Already Created.'.$file_name);
                }
            }
        }
    }

    private function _isMigrationAlreadyExists($file_name)
    {
        $dest_dir_files = scandir(base_path('database/migrations'));
        $has = false;
        foreach ($dest_dir_files as $file) {
            $file_name_arr = explode('.', $file);
            $ext = end($file_name_arr);
            if ($ext === 'php') {
                $_file_name = preg_replace('/^[0-9]+[0-9\_]+/', '', $file);
                if ($_file_name == $file_name) {
                    $has = true;
                    break;
                }
            }
        }

        return $has;
    }
}
