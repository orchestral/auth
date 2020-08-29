<?php

namespace Orchestra\Auth\Passwords;

use Closure;
use Illuminate\Auth\Passwords\PasswordBroker as Broker;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\UserProvider;

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
     */
    public function sendResetLink(array $credentials, ?Closure $callback = null): string
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.

        if (\is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if ($this->tokens->recentlyCreatedToken($user)) {
            return static::RESET_THROTTLED;
        }

        $token = $this->tokens->create($user);

        if ($callback) {
            $callback($user, $token);
        } else {
            // Once we have the reset token, we are ready to send the message out to this
            // user with a link to reset their password. We will then redirect back to
            // the current URI having nothing set in the session to indicate errors.
            $user->sendPasswordResetNotification($token, $this->provider);
        }


        return static::RESET_LINK_SENT;
    }
}
