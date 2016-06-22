<?php

namespace Orchestra\Auth\Passwords;

use Orchestra\Notifier\Message;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Auth\Passwords\PasswordBroker as Broker;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;

class PasswordBroker extends Broker
{
    /**
     * Create a new password broker instance.
     *
     * @param  \Illuminate\Auth\Passwords\TokenRepositoryInterface  $tokens
     * @param  \Illuminate\Contracts\Auth\UserProvider  $users
     */
    public function __construct(
        TokenRepositoryInterface $tokens,
        UserProvider $users
    ) {
        $this->users  = $users;
        $this->tokens = $tokens;
    }

    /**
     * Send a password reminder to a user.
     *
     * @param  array  $credentials
     * @param  \Closure  $callback
     *
     * @return string
     */
    public function sendResetLink(array $credentials)
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        // Once we have the reminder token, we are ready to send a message out to the
        // user with a link to reset their password. We will then redirect back to
        // the current URI having nothing set in the session to indicate errors.
        $user->sendPasswordResetNotification(
            $this->tokens->create($user)
        );

        return static::RESET_LINK_SENT;
    }
}
