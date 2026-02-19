<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Shared\Services\TenantManager::class, function ($app) {
            return new \App\Shared\Services\TenantManager();
        });

        $this->app->bind(\App\Shared\Interfaces\SmsGatewayInterface::class, \App\Shared\Services\Sms\NextSmsGateway::class);
        $this->app->bind(\App\Shared\Interfaces\EmailSenderInterface::class, \App\Shared\Services\Email\TenantEmailSender::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
