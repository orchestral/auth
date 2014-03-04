<?php namespace Orchestra\Auth;

class AuthManager extends \Illuminate\Auth\AuthManager
{
    /**
     * {@inheritdoc}
     */
    protected function callCustomCreator($driver)
    {
        $custom = parent::callCustomCreator($driver);

        if ($custom instanceof Guard) {
            return $custom;
        }

        return new Guard($custom, $this->app['session.store']);
    }

    /**
     * {@inheritdoc}
     */
    public function createDatabaseDriver()
    {
        $provider = $this->createDatabaseProvider();

        return new Guard($provider, $this->app['session.store']);
    }

    /**
     * {@inheritdoc}
     */
    public function createEloquentDriver()
    {
        $provider = $this->createEloquentProvider();

        return new Guard($provider, $this->app['session.store']);
    }
}
