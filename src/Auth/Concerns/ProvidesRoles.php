<?php

namespace Orchestra\Auth\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Auth\Authenticatable;

trait ProvidesRoles
{
    /**
     * Cached user to roles relationship.
     *
     * @var array
     */
    protected $userRoles = [];

    /**
     * Get the current user's roles of the application.
     *
     * If the user is a guest, empty array should be returned.
     *
     * @return \Illuminate\Support\Collection
     */
    public function roles(): Collection
    {
        $user = $this->user();
        $userId = 0;

        // This is a simple check to detect if the user is actually logged-in,
        // otherwise it's just as the same as setting userId as 0.
        \is_null($user) || $userId = $user->getAuthIdentifier();

        $roles = $this->userRoles["{$userId}"] ?? new Collection();

        // This operation might be called more than once in a request, by
        // cached the event result we can avoid duplicate events being fired.
        if ($roles->isEmpty()) {
            $roles = $this->getUserRolesFromEventDispatcher($user, $roles);
        }

        $this->userRoles["{$userId}"] = $roles;

        return $roles;
    }

    /**
     * Ger user roles from event dispatcher.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \Illuminate\Support\Collection|array  $roles
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getUserRolesFromEventDispatcher(Authenticatable $user = null, $roles = []): Collection
    {
        $roles = $this->events->until('orchestra.auth: roles', [$user, $roles]);

        // It possible that after event are all propagated we don't have a
        // roles for the user, in this case we should properly append "Guest"
        // user role to the current user.

        return new Collection($roles ?? ['Guest']);
    }
}
