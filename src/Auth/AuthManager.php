<?php namespace Orchestra\Auth;

use Illuminate\Auth\AuthManager as BaseManager;

class AuthManager extends BaseManager
{
    /**
     * Create a session based authentication guard.
     *
     * @param  string  $name
     * @param  array  $config
     * @return \Illuminate\Auth\SessionGuard
     */
    public function createSessionDriver($name, $config)
    {
        $provider = $this->createUserProvider($config['source']);

        $guard = new SessionGuard($name, $provider, $this->app->make('session.store'));

        // When using the remember me functionality of the authentication services we
        // will need to be set the encryption instance of the guard, which allows
        // secure, encrypted cookie values to get generated for those cookies.
        if (method_exists($guard, 'setCookieJar')) {
            $guard->setCookieJar($this->app->make('cookie'));
        }

        if (method_exists($guard, 'setDispatcher')) {
            $guard->setDispatcher($this->app->make('events'));
        }

        if (method_exists($guard, 'setRequest')) {
            $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));
        }

        return $guard;
    }
}
