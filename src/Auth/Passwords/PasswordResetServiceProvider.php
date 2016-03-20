<?php

namespace Orchestra\Auth\Passwords;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider as ServiceProvider;

class PasswordResetServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the password broker instance.
     *
     * @return void
     */
    protected function registerPasswordBroker()
    {
        $this->app->singleton('auth.password', function (Application $app) {
            return new PasswordBrokerManager($app);
        });

        $this->app->bind('auth.password.broker', function (Application $app) {
            return $app->make('auth.password')->broker();
        });
    }
}
