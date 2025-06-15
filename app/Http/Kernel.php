<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;


class Kernel extends HttpKernel
{
    // Middleware'ler burada tanımlanır
    protected $routeMiddleware = [
        // Diğer middleware'ler...
        // 'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        // 'admin' => \App\Http\Middleware\AdminRoleMiddleware::class,
        // 'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        // 'auth.admin' => \App\Http\Middleware\AuthenticateAdmin::class,
        // 'auth.seller' => \App\Http\Middleware\AuthenticateSeller::class,
        // 'auth.admin' => \App\Http\Middleware\RedirectIfNotAuthenticated::class,
        // 'auth.seller' => \App\Http\Middleware\RedirectIfNotAuthenticated::class,
        'auth' => \App\Http\Middleware\CustomAuthenticate::class,
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        'api' => [
            \Fruitcake\Cors\HandleCors::class,
            // ...
        ],
    ];

}
