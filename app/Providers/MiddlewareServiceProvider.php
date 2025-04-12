<?php

namespace App\Providers;

use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register the middleware with the application.
     */
    public function boot(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('admin', EnsureUserIsAdmin::class);
    }
}