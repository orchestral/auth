<?php

namespace Orchestra\Auth;

use Illuminate\Auth\SessionGuard as BaseGuard;
use Orchestra\Contracts\Auth\Guard as GuardContract;

class SessionGuard extends BaseGuard implements GuardContract
{
    use Concerns\HasRoles,
        Concerns\ProvidesRoles;

    /**
     * Setup roles event listener.
     *
     * @param  \Closure|string  $event
     *
     * @return void
     *
     * @deprecated v3.8.x and will be removed in v4.0.0
     */
    public function setup($event): void
    {
        $this->userRoles = [];

        $this->events->forget('orchestra.auth: roles');
        $this->events->listen('orchestra.auth: roles', $event);
    }

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
