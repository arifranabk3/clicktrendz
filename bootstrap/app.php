<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SyncRegionMiddleware::class,
        ]);
        
        $middleware->alias([
            'admin' => \App\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withSchedule(function ($schedule) {
        $schedule->call(function () {
            app(\App\Services\ReportingService::class)->generateAndSendReport();
        })->dailyAt('07:30');

        $schedule->call(function () {
            app(\App\Services\ReportingService::class)->generateAndSendReport();
        })->dailyAt('13:15');

        $schedule->call(function () {
            app(\App\Services\ReportingService::class)->generateAndSendReport();
        })->dailyAt('19:30');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
