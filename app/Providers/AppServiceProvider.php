<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Older MySQL/MariaDB versions choke on VARCHAR(255) unique indexes when
        // using utf8mb4 (4 bytes × 255 = 1020 bytes, exceeds the 767/1000 byte
        // key-length limit). Cap the default string length to 191 so
        // VARCHAR(191) × 4 = 764 bytes fits under the limit.
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
    }
}
