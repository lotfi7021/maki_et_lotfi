<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SwaggerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            set_error_handler(function ($errno, $errstr) {
                if (str_contains($errstr, 'PathItem') || 
                    str_contains($errstr, 'Skipping') ||
                    str_contains($errstr, 'Required')) {
                    return true; // ignorer ce warning
                }
                return false;
            }, E_USER_WARNING | E_USER_NOTICE);
        }
    }

    public function boot(): void {}
}