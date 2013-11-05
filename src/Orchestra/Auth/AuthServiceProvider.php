<?php namespace Orchestra\Auth;

use Illuminate\Auth\AuthServiceProvider as ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->registerAuth();
        $this->registerAcl();
        $this->registerAuthListener();
    }

    /**
     * Register the service provider for Auth.
     *
     * @return void
     */
    protected function registerAuth()
    {
        $this->app['auth'] = $this->app->share(function ($app) {
            // Once the authentication service has actually been requested by the developer
            // we will set a variable in the application indicating such. This helps us
            // know that we need to set any queued cookies in the after event later.
            $app['auth.loaded'] = true;

            return new AuthManager($app);
        });
    }

    /**
     * Register the service provider for Acl.
     *
     * @return void
     */
    protected function registerAcl()
    {
        $this->app['orchestra.acl'] = $this->app->share(function ($app) {
            return new Acl\Environment($app['auth']->driver());
        });
    }

    /**
     * We need to ensure that Orchestra\Acl is compliance with our Eloquent
     * Model, This would overwrite the default configuration.
     *
     * @return void
     */
    protected function registerAuthListener()
    {
        $this->app['events']->listen('orchestra.auth: roles', function ($user, $roles) {
            // When user is null, we should expect the roles is not
            // available. Therefore, returning null would propagate any
            // other event listeners (if any) to try resolve the roles.
            if (is_null($user)) {
                return ;
            }

            foreach ($user->roles()->get() as $role) {
                array_push($roles, $role->name);
            }

            return $roles;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->package('orchestra/auth', 'orchestra/auth');

        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return array('auth', 'orchestra.acl');
    }
}
