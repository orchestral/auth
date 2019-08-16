<?php

namespace Orchestra\Auth\Passwords;

use Illuminate\Contracts\Container\Container;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider as ServiceProvider;

class PasswordResetServiceProvider extends ServiceProvider
{
    /**
     * Register the password broker instance.
     *
     * @return void
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
