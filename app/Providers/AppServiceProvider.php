<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// 1. PASTIKAN BARIS INI ADA (PENTING!)
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Biarkan kosong atau isi bawaannya
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 2. MASUKKAN KODENYA DI SINI (HANYA SATU FUNGSI BOOT)
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
