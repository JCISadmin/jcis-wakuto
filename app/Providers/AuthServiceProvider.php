<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\SessionGuard;

use App\Providers\AuthUserProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::extend('manage', function($app, $name, array $config) {

            $provider = Auth::createUserProvider($config['provider']);
            $request = $app->make('request');

            return new SessionGuard(
                $name,
                $provider,
                $request->session(),
                $request
            );

        });

        Auth::provider(
            'authUser',
            function($app, array $config) {
                $connection = $this->app['db']->connection();
                return new AuthUserProvider($connection, $this->app['hash'], $config['table']);
            }
        );

    }

}
