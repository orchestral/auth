<?php

namespace Orchestra\Auth\Passwords;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Auth\Passwords\PasswordBroker as Broker;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;

class PasswordBroker extends Broker
{
    /**
     * The password reset user provider.
     *
     * @var string|null
     */
    protected $provider;

    /**
     * Create a new password broker instance.
     *
     * @param  \Illuminate\Auth\Passwords\TokenRepositoryInterface  $tokens
     * @param  \Illuminate\Contracts\Auth\UserProvider  $users
     * @param  string  $provider
     */
    public function __construct(
        TokenRepositoryInterface $tokens,
        UserProvider $users,
        $provider
    ) {
        $this->users = $users;
        $this->tokens = $tokens;
        $this->provider = $provider;
    }

    /**
     * Send a password reminder to a user.
     *
     * @param  array  $credentials
     *
     * @return string
     */
    public function sendResetLink(array $credentials): string
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.

        if (\is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        // Once we have the reminder token, we are ready to send a message out to the
        // user with a link to reset their password. We will then redirect back to
        // the current URI having nothing set in the session to indicate errors.
        $user->sendPasswordResetNotification(
            $this->tokens->create($user), $this->provider
        );

        return static::RESET_LINK_SENT;
    }
}
