<?php

namespace Orchestra\Auth;

use Illuminate\Auth\AuthServiceProvider as ServiceProvider;
use Illuminate\Contracts\Container\Container;
use Orchestra\Authorization\Policy;
use Orchestra\Contracts\Authorization\Factory as FactoryContract;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerPolicyAfterResolvingHandler();
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $path = \realpath(__DIR__.'/../../');

        $this->loadMigrationsFrom([
            "{$path}/database/migrations",
        ]);
    }

    /**
     * Register the service provider for Auth.
     */
    protected function registerAuthenticator(): void
    {
        $this->app->singleton('auth', static function (Container $app) {
            return new AuthManager($app);
        });

        $this->app->singleton('auth.driver', static function (Container $app) {
            return $app->make('auth')->guard();
        });
    }

    /**
     * Register the Policy after resolving handler.
     */
    protected function registerPolicyAfterResolvingHandler(): void
    {
        $this->app->afterResolving(Policy::class, function (Policy $policy) {
            return $policy->setAuthorization($this->app->make(FactoryContract::class));
        });
    }
}
