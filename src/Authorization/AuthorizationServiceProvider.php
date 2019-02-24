<?php

namespace Orchestra\Authorization;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Contracts\Support\RegistrableProvider;

class AuthorizationServiceProvider extends ServiceProvider implements DeferrableProvider, RegistrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('orchestra.acl', function (Application $app) {
            return new Factory($app->make('auth.driver'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['orchestra.acl'];
    }
}
