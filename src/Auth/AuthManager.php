<?php

namespace Orchestra\Auth;

use Illuminate\Auth\AuthManager as BaseManager;

class AuthManager extends BaseManager
{
    /**
     * Create a session based authentication guard.
     *
     * @param  string  $name
     * @param  array  $config
     *
     * @return \Orchestra\Auth\SessionGuard
     */
    public function createSessionDriver($name, $config)
    {
        $provider = $this->createUserProvider($config['provider']);

        $guard = new SessionGuard($name, $provider, $this->app->make('session.store'));

        // When using the remember me functionality of the authentication services we
        // will need to be set the encryption instance of the guard, which allows
        // secure, encrypted cookie values to get generated for those cookies.

        if (\method_exists($guard, 'setCookieJar')) {
            $guard->setCookieJar($this->app->make('cookie'));
        }

        if (\method_exists($guard, 'setDispatcher')) {
            $guard->setDispatcher($this->app->make('events'));
        }

        if (\method_exists($guard, 'setRequest')) {
            $guard->setRequest($this->app->refresh('request', $guard, 'setRequest'));
        }

        return $guard;
    }

    /**
     * Register a new callback based request guard.
     *
     * @param  string  $driver
     * @param  callable  $callback
     *
     * @return $this
     */
    public function viaRequest($driver, callable $callback)
    {
        return $this->extend($driver, function () use ($callback) {
            $guard = new RequestGuard($callback, $this->app['request'], $this->createUserProvider());

            $this->app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }

    /**
     * Create an instance of the Eloquent user provider.
     *
     * @param  array  $config
     *
     * @return \Orchestra\Auth\EloquentUserProvider
     */
    protected function createEloquentProvider($config)
    {
        return new EloquentUserProvider($this->app->make('hash'), $config['model']);
    }
}
