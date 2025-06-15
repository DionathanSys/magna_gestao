<?php

namespace App\Providers;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Model;
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
        Model::unguard();
        FilamentColor::register([
            'yellow' => Color::hex('#e8fc03'),
            'black' => Color::hex('#000000'),
            'red' => Color::hex('#fc0303'),
            'blue' => Color::hex('#2e00fa'),
        ]);
    }
}
