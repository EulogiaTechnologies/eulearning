<?php

namespace App\Console\Commands;

use App\Traits\UploadTheme;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AppReset extends Command
{
    use UploadTheme;

    protected $signature = 'app:reset';


    protected $description = 'Reset Database';


    public function handle()
    {
        if (!env('DEMO_MODE')) {
            return false;
        }
        //stat resetting
        $this->startResetting();
        //reset database
        $this->migrateFresh();

        // seed data
        $this->dbSeed();

        // install passport
        $this->passportInstall();

        // generate new key
        $this->generateNewKey();

        //remove uploaded image files
        $this->removeUploadedImageFile();

        //reset css & js file
        $this->resetCustomCssJsFiles();

        //end resetting
        $this->endResetting();

        Log::alert('Reset Command run at ' . now()->toDateTimeString());

    }

    public function startResetting()
    {
        Storage::put('.app_resetting', '');
    }

    public function endResetting()
    {
        Storage::delete('.app_resetting');
    }

    protected function migrateFresh()
    {
        Artisan::call('db:wipe', array('--force' => true));
        Artisan::call('migrate', array('--force' => true));
    }

    protected function dbSeed()
    {
        Artisan::call('db:seed', array('--force' => true));
    }

    protected function passportInstall()
    {
        Artisan::call('migrate', [
            '--path' => 'vendor/laravel/passport/database/migrations',
            '--force' => true,

        ]);
        Artisan::call('passport:install');
    }

    protected function generateNewKey()
    {
        Artisan::call('key:generate', ['--force' => true]);
    }

    public function resetCustomCssJsFiles()
    {
        $css_path = 'public/frontend/infixlmstheme/css/custom.css';
        $js_path = 'public/frontend/infixlmstheme/js/custom.js';
        File::put($css_path, "");
        File::put($js_path, "");
    }

    public function removeUploadedImageFile()
    {
        $path = 'public/uploads/main/images';
        $this->delete_directory($path);

        $path = 'public/uploads/main/file';
        $this->delete_directory($path);

        $path = 'public/database-backup';
        $this->delete_directory($path);
    }

    public function utitity()
    {
        Artisan::call('optimize:clear');
        File::delete(File::glob('bootstrap/cache/*.php'));
        File::delete(File::glob('storage/framework/laravel-excel/*'));

        array_map('unlink', array_filter((array)glob(storage_path('logs/*.log'))));
        array_map('unlink', array_filter((array)glob(storage_path('debugbar/*.json'))));

        envu([
            'APP_DEBUG' => "false",
            'FORCE_HTTPS' => "false",
        ]);

    }
}
