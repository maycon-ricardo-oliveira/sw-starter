<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\JWTGuard;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Auth::extend('jwt', function ($app, $name, array $config) {
            return new JWTGuard(
                new JWT($app['tymon.jwt.manager'], $app['tymon.jwt.parser']),
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );
        });
    }
}
