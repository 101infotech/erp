<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'workspace' => \App\Http\Middleware\SetWorkspace::class,
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'employee' => \App\Http\Middleware\EnsureUserIsEmployee::class,
            'can.manage.leads' => \App\Http\Middleware\EnsureCanManageLeads::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'maintenance' => \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        // Apply maintenance mode check to web and api routes
        $middleware->web(append: [
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        // Enable stateful API requests (session-based authentication for same-origin requests)
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
