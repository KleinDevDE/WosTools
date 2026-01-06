<?php

use App\Jobs\SyncCharacterStatsJob;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use \Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn (Request $request) => route('auth.login'));
        $middleware->appendToGroup('web', \App\Http\Middleware\NotificationSession::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\LoadCharacter::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\SetLocale::class);

        // Sanctum SPA authentication
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);
    })->withSchedule(function (Schedule $schedule): void {
        $schedule->command('backup:run')->daily()->name('Backup')->withoutOverlapping();
        $schedule->call(function() {
            Bus::batch(SyncCharacterStatsJob::prepareBatch())->name('Sync character stats')->dispatch();
        })->daily()->name('Sync character stats')->withoutOverlapping();
    })
    ->create();
