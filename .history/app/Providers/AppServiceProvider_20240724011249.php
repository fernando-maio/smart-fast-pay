<?php

namespace App\Providers;

use App\Interface\PaymentServiceInterface;
use App\Services\PaymentService;
use App\Services\PaymentServiceInterface as ServicesPaymentServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ServicesPaymentServiceInterface::class, PaymentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
