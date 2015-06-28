<?php namespace Orchestra\Auth;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Auth\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
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
            return $app->make('auth')->driver();
        });
    }
}
