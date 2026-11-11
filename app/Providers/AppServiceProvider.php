<?php

namespace App\Providers;

use App\Models\FeeInvoice;
use App\Models\ParentProfile;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
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
        Paginator::useTailwind();

        Route::bind('parent', fn (string $value) => ParentProfile::query()->findOrFail($value));
        Route::bind('fee', fn (string $value) => FeeInvoice::query()->findOrFail($value));
    }
}
