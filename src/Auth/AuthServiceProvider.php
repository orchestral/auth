<?php

namespace Orchestra\Auth;

use Orchestra\Authorization\Policy;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Auth\AuthServiceProvider as ServiceProvider;
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
        $path = realpath(__DIR__.'/../../');

        $this->loadMigrationsFrom([
            "{$path}/resources/database/migrations",
        ]);
    }

    /**
     * Register the service provider for Auth.
     *
     * @return void
     */
    protected function registerAuthenticator()
    {
        $this->app->singleton('auth', function (Application $app) {
            // Once the authentication service has actually been requested by the developer
            // we will set a variable in the application indicating such. This helps us
            // know that we need to set any queued cookies in the after event later.
            $app['auth.loaded'] = true;

            return new AuthManager($app);
        });

        $this->app->singleton('auth.driver', function (Application $app) {
            return $app->make('auth')->guard();
        });
    }

    /**
     * Register the Policy after resolving handler.
     *
     * @return void
     */
    protected function registerPolicyAfterResolvingHandler()
    {
        $this->app->afterResolving(Policy::class, function (Policy $policy) {
            return $policy->setAuthorization($this->app->make(FactoryContract::class));
        });
    }
}
