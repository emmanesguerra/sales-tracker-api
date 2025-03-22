<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\Auth\AuthRepository;
use App\Repositories\Auth\TenantRepositoryInterface;
use App\Repositories\Auth\TenantRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
