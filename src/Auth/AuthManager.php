<?php namespace Orchestra\Auth;

use Illuminate\Auth\AuthManager as BaseManager;

class AuthManager extends BaseManager
{
    /**
     * {@inheritdoc}
     */
    protected function callCustomCreator($driver)
    {
        $custom = $this->customCreators[$driver]($this->app);

        if ($custom instanceof Guard) {
            return $custom;
        }

        return new Guard($custom, $this->app->make('session.store'));
    }

    /**
     * {@inheritdoc}
     */
    public function createDatabaseDriver()
    {
        $provider = $this->createDatabaseProvider();

        return new Guard($provider, $this->app->make('session.store'));
    }

    /**
     * {@inheritdoc}
     */
    public function createEloquentDriver()
    {
        $provider = $this->createEloquentProvider();

        return new Guard($provider, $this->app->make('session.store'));
    }
}
