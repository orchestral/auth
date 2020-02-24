<?php

namespace Orchestra\Auth\Passwords;

use Illuminate\Auth\Passwords\PasswordResetServiceProvider as ServiceProvider;
use Illuminate\Contracts\Container\Container;

class PasswordResetServiceProvider extends ServiceProvider
{
    /**
     * Register the password broker instance.
     */
    protected function registerPasswordBroker(): void
    {
        $this->app->singleton('auth.password', static function (Container $app) {
            return new PasswordBrokerManager($app);
        });

        $this->app->bind('auth.password.broker', static function (Container $app) {
            return $app->make('auth.password')->broker();
        });
    }
}
