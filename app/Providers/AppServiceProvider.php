<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\Auth\AuthRepository;
use App\Repositories\Auth\TenantRepositoryInterface;
use App\Repositories\Auth\TenantRepository;
use App\Repositories\Item\ItemRepositoryInterface;
use App\Repositories\Item\ItemRepository;
use App\Repositories\Sales\SalesRepository;
use App\Repositories\Sales\SalesRepositoryInterface;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
        $this->app->bind(ItemRepositoryInterface::class, ItemRepository::class);
        $this->app->bind(SalesRepositoryInterface::class, SalesRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
