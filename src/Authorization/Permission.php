<?php

namespace Orchestra\Authorization;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Orchestra\Contracts\Authorization\Authorizable;

trait Permission
{
    /**
     * Auth instance.
     *
     * @var \Orchestra\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * List of roles.
     *
     * @var \Orchestra\Authorization\Role
     */
    protected $roles;

    /**
     * List of actions.
     *
     * @var \Orchestra\Authorization\Action
     */
    protected $actions;

    /**
     * List of ACL map between roles and action.
     *
     * @var array
     */
    protected $acl = [];

    /**
     * User roles.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $userRoles;

    /**
     * Verify whether given roles has sufficient roles to access the
     * actions based on available type of access.
     *
     * @param  string|array  $roles
     * @param  string        $action     A string of action name
     *
     * @throws \InvalidArgumentException
     */
    public function checkAuthorization($roles, $action): bool
    {
        $name = $action;
        $action = $this->actions->search($name);

        if (\is_null($action)) {
            throw new InvalidArgumentException("Unable to verify unknown action {$name}.");
        }

        $authorized = false;

        $roles = new Collection($roles);

        foreach ($roles->all() as $role) {
            $role = $this->roles->search($role);
            $permission = $this->acl[$role.':'.$action] ?? false;

            // array_search() will return false when no key is found based
            // on given haystack, therefore we should just ignore and
            // continue to the next role.
            if (! \is_null($role) && $permission === true) {
                $authorized = true;
            }
        }

        return $authorized;
    }

    /**
     * Assign single or multiple $roles + $actions to have access.
     *
     * @param  string|array  $roles
     * @param  string|array  $actions
     *
     * @throws \InvalidArgumentException
     */
    public function setAuthorization($roles, $actions, bool $allow = true): void
    {
        $roles = $this->roles->filter($roles);
        $actions = $this->actions->filter($actions);

        foreach ($roles as $role) {
            if (! $this->roles->has($role)) {
                throw new InvalidArgumentException("Role {$role} does not exist.");
            }

            $this->groupedAssignAction($role, $actions, $allow);
        }
    }

    /**
     * Grouped assign actions to have access.
     *
     * @param  string  $role
     *
     * @throws \InvalidArgumentException
     */
    protected function groupedAssignAction($role, array $actions, bool $allow = true): bool
    {
        foreach ($actions as $action) {
            if (! $this->actions->has($action)) {
                throw new InvalidArgumentException("Action {$action} does not exist.");
            }

            $this->assign($role, $action, $allow);
        }

        return true;
    }

    /**
     * Assign a key combination of $roles + $actions to have access.
     */
    protected function assign(?string $role = null, ?string $action = null, bool $allow = true): void
    {
        $role = $this->roles->findKey($role);
        $action = $this->actions->findKey($action);

        if (! \is_null($role) && ! \is_null($action)) {
            $this->acl["{$role}:{$action}"] = $allow;
        }
    }

    /**
     * Assign user instance.
     *
     * @return $this
     */
    public function setUser(Authorizable $user)
    {
        $userRoles = $user->getRoles();

        $this->userRoles = $userRoles;

        return $this;
    }

    /**
     * Revoke assigned user instance.
     *
     * @return $this
     */
    public function revokeUser()
    {
        $this->userRoles = null;

        return $this;
    }

    /**
     * Get the ACL collection.
     */
    public function acl(): array
    {
        return $this->acl;
    }

    /**
     * Get the auth implementation.
     */
    public function auth(): Guard
    {
        return $this->auth;
    }

    /**
     * Get the `actions` instance.
     *
     * @return \Orchestra\Authorization\Action
     */
    public function actions(): Action
    {
        return $this->actions;
    }

    /**
     * Get the `roles` instance.
     *
     * @return \Orchestra\Authorization\Role
     */
    public function roles(): Role
    {
        return $this->roles;
    }

    /**
     * Get all possible user roles.
     */
    protected function getUserRoles(): Collection
    {
        if (! \is_null($this->userRoles)) {
            return $this->userRoles;
        } elseif (! $this->auth->guest()) {
            if ($this->auth instanceof \Orchestra\Contracts\Auth\Guard) {
                return $this->auth->roles();
            }

            return $this->auth->user()->getRoles();
        }

        return new Collection($this->roles->has('guest') ? ['guest'] : []);
    }
}
