<?php

namespace Orchestra\Auth\Concerns;

trait HasRoles
{
    /**
     * Determine if current user has the given role.
     *
     * @param  string|array  $roles
     *
     * @return bool
     */
    public function is($roles): bool
    {
        $userRoles = $this->roles()->all();

        // For a pre-caution, we should return false in events where user
        // roles not an array.
        if (! \is_array($userRoles)) {
            return false;
        }

        // We should ensure that all given roles match the current user,
        // consider it as a AND condition instead of OR.
        foreach ((array) $roles as $role) {
            if (! \in_array($role, $userRoles)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if current user has any of the given role.
     *
     * @param  array  $roles
     *
     * @return bool
     */
    public function isAny(array $roles): bool
    {
        $userRoles = $this->roles()->all();

        // For a pre-caution, we should return false in events where user
        // roles not an array.
        if (! \is_array($userRoles)) {
            return false;
        }

        // We should ensure that any given roles match the current user,
        // consider it as OR condition.
        foreach ($roles as $role) {
            if (\in_array($role, $userRoles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if current user does not has any of the given role.
     *
     * @param  string|array  $roles
     *
     * @return bool
     */
    public function isNot($roles): bool
    {
        return ! $this->is($roles);
    }

    /**
     * Determine if current user does not has any of the given role.
     *
     * @param  string|array  $roles
     *
     * @return bool
     */
    public function isNotAny($roles): bool
    {
        return ! $this->isAny($roles);
    }
}
