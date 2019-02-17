<?php

namespace Orchestra\Auth\Passwords;

use InvalidArgumentException;
use Illuminate\Auth\Passwords\PasswordBrokerManager as BaseManager;

class PasswordBrokerManager extends BaseManager
{
    /**
     * Resolve the given broker.
     *
     * @param  string  $name
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function resolve($name): PasswordBroker
    {
        if (\is_null($config = $this->getConfig($name))) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        // The password broker uses a token repository to validate tokens and send user
        // password e-mails, as well as validating that password reset process as an
        // aggregate service of sorts providing a convenient interface for resets.
        return new PasswordBroker(
            $this->createTokenRepository($config),
            $this->app->make('auth')->createUserProvider($config['provider']),
            $name
        );
    }
}
