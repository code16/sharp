<?php

namespace App\Providers;

use App\Sharp\Auth\EloquentSharpUserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        auth()->provider('eloquent.sharp', function($app, array $config) {
            return new EloquentSharpUserProvider($app['hash'], $config['model']);
        });
    }
}
