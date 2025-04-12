<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Konfigurasi khusus Sanctum untuk API
        Sanctum::authenticateAccessTokensUsing(
            fn (PersonalAccessToken $accessToken, bool $isValid) => $isValid
        );

        // Jika ingin token tidak pernah kadaluarsa
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        
        // Definisi Gate/Policies (jika diperlukan)
        Gate::define('admin', function ($user) {
            return $user->is_admin === true;
        });
    }
}