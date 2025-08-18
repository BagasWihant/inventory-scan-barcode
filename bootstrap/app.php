<?php

use App\Http\Middleware\InputStockTakingMiddleware;
use App\Http\Middleware\isMC;
use App\Http\Middleware\UpdateActivity;
use Illuminate\Foundation\Application;
use App\Http\Middleware\IsPrepareStockTaking;
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
        $middleware->alias([
            'isPrepareStockTaking' => IsPrepareStockTaking::class,
            'isMC' =>isMC::class,
            'inputStockTaking' => InputStockTakingMiddleware::class,
            'updateActivity' => UpdateActivity::class,
            'allowednik' => \App\Http\Middleware\allowedNIK::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
