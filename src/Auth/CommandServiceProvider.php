<?php

namespace Orchestra\Auth;

use Illuminate\Support\ServiceProvider;
use Orchestra\Auth\Console\AuthCommand;

class CommandServiceProvider extends ServiceProvider
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
        $this->app->singleton('orchestra.commands.auth', function () {
            return new AuthCommand();
        });

        $this->commands('orchestra.commands.auth');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['orchestra.commands.auth'];
    }
}
