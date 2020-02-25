<?php

namespace Orchestra\Auth;

use Illuminate\Auth\SessionGuard as BaseGuard;
use Orchestra\Contracts\Auth\Guard as GuardContract;

class SessionGuard extends BaseGuard implements GuardContract
{
    use Concerns\HasRoles,
        Concerns\ProvidesRoles;

    /**
     * {@inheritdoc}
     */
    public function logout(): void
    {
        parent::logout();

        // We should flush the cached user roles relationship so any
        // subsequent request would re-validate all information,
        // instead of referring to the cached value.
        $this->userRoles = [];
    }
}
