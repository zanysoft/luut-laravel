<?php

namespace App\Providers;

use App\Extensions\CacheFileStore;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->booting(function () {
            Cache::extend('file', function (Application $app) {
                return Cache::repository(new CacheFileStore($app['files'], config('cache.stores.file.path'), null));
            });
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
