<?php

namespace Orchestra\Auth;

use Illuminate\Auth\EloquentUserProvider as UserProvider;

class EloquentUserProvider extends UserProvider
{
    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();
        $model = $model->newQuery()
                    ->where($model->getAuthIdentifierName(), $identifier)
                    ->first();

        if (! $model) {
            return;
        }

        $rememberToken = $model->getRememberToken();

        return $rememberToken && \hash_equals($rememberToken, $token) ? $model : null;
    }
}
