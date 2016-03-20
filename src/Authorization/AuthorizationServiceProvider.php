<?php

namespace Orchestra\Authorization;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

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
