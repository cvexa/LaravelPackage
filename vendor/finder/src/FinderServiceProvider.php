<?php
    // MyVendor\contactform\src\ContactFormServiceProvider.php
namespace vendor\finder\src;

use Illuminate\Support\ServiceProvider;

class FinderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'finder');
        app()->config["filesystems.disks.publicDisk"] = [
            'driver' => 'local',
            'root' => public_path(),
        ];
    }
    public function register()
    {
    }
}