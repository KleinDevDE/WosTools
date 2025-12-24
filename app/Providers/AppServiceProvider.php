<?php

namespace App\Providers;

use Filament\Notifications\Livewire\DatabaseNotifications;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
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
        DatabaseNotifications::trigger('notifications-trigger');
//        $this->app->register(CustomLivewireServiceProvider::class);

        // Configure Filament colors to match unified palette
        FilamentColor::register([
            'primary' => [
                50 => 'oklch(0.95 0.05 200)',
                100 => 'oklch(0.90 0.10 200)',
                200 => 'oklch(0.85 0.15 200)',
                300 => 'oklch(0.75 0.20 200)',
                400 => 'oklch(0.65 0.20 200)',
                500 => 'oklch(0.55 0.20 200)',
                600 => 'oklch(0.45 0.20 200)',
                700 => 'oklch(0.40 0.18 200)',
                800 => 'oklch(0.35 0.15 200)',
                900 => 'oklch(0.25 0.10 200)',
                950 => 'oklch(0.20 0.08 200)',
            ],
            'gray' => [
                50 => 'oklch(0.95 0.02 250)',
                100 => 'oklch(0.90 0.02 250)',
                200 => 'oklch(0.80 0.02 250)',
                300 => 'oklch(0.70 0.02 250)',
                400 => 'oklch(0.60 0.02 250)',
                500 => 'oklch(0.50 0.02 250)',
                600 => 'oklch(0.40 0.02 250)',
                700 => 'oklch(0.30 0.02 250)',
                800 => 'oklch(0.25 0.02 250)',
                900 => 'oklch(0.20 0.02 250)',
                950 => 'oklch(0.15 0.02 250)',
            ],
            'success' => [
                50 => 'oklch(0.95 0.05 150)',
                100 => 'oklch(0.90 0.10 150)',
                200 => 'oklch(0.85 0.15 150)',
                300 => 'oklch(0.75 0.20 150)',
                400 => 'oklch(0.65 0.20 150)',
                500 => 'oklch(0.55 0.20 150)',
                600 => 'oklch(0.45 0.20 150)',
                700 => 'oklch(0.40 0.18 150)',
                800 => 'oklch(0.35 0.15 150)',
                900 => 'oklch(0.25 0.10 150)',
                950 => 'oklch(0.20 0.08 150)',
            ],
            'danger' => [
                50 => 'oklch(0.95 0.05 25)',
                100 => 'oklch(0.90 0.10 25)',
                200 => 'oklch(0.85 0.15 25)',
                300 => 'oklch(0.75 0.20 25)',
                400 => 'oklch(0.65 0.20 25)',
                500 => 'oklch(0.55 0.20 25)',
                600 => 'oklch(0.45 0.20 25)',
                700 => 'oklch(0.40 0.18 25)',
                800 => 'oklch(0.35 0.15 25)',
                900 => 'oklch(0.25 0.10 25)',
                950 => 'oklch(0.20 0.08 25)',
            ],
            'warning' => [
                50 => 'oklch(0.95 0.05 65)',
                100 => 'oklch(0.90 0.10 65)',
                200 => 'oklch(0.85 0.15 65)',
                300 => 'oklch(0.75 0.20 65)',
                400 => 'oklch(0.70 0.20 65)',
                500 => 'oklch(0.65 0.20 65)',
                600 => 'oklch(0.55 0.20 65)',
                700 => 'oklch(0.45 0.18 65)',
                800 => 'oklch(0.35 0.15 65)',
                900 => 'oklch(0.25 0.10 65)',
                950 => 'oklch(0.20 0.08 65)',
            ],
            'info' => [
                50 => 'oklch(0.95 0.05 240)',
                100 => 'oklch(0.90 0.10 240)',
                200 => 'oklch(0.85 0.15 240)',
                300 => 'oklch(0.75 0.20 240)',
                400 => 'oklch(0.65 0.20 240)',
                500 => 'oklch(0.55 0.20 240)',
                600 => 'oklch(0.45 0.20 240)',
                700 => 'oklch(0.40 0.18 240)',
                800 => 'oklch(0.35 0.15 240)',
                900 => 'oklch(0.25 0.10 240)',
                950 => 'oklch(0.20 0.08 240)',
            ],
        ]);
    }
}
