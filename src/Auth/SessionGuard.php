<?php

namespace Orchestra\Auth;

use Illuminate\Support\Collection;
use Illuminate\Auth\SessionGuard as BaseGuard;
use Illuminate\Contracts\Auth\Authenticatable;
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
