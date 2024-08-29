<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FileService;
use App\Services\TtsExcelService;
use App\Services\AuthService;
use App\Services\ValidationService;


/**
 * Class ApiServiceProvider
 * @package App\Providers
 */
class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AuthService::class, function() {
            return new AuthService();
        });

        $this->app->singleton(ValidationService::class, function() {
            return new ValidationService();
        });

        $this->app->singleton(FileService::class, function() {
            return new FileService();
        });

        $this->app->singleton(TtsExcelService::class, function() {
            return new TtsExcelService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
