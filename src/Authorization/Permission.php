<?php

namespace Orchestra\Authorization;

use InvalidArgumentException;
use Illuminate\Support\Collection;
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
     * @var \Orchestra\Authorization\Fluent
     */
    protected $roles;

    /**
     * List of actions.
     *
     * @var \Orchestra\Authorization\Fluent
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
     * @param  string|array  $roles      A string or an array of roles
     * @param  string        $action     A string of action name
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function checkAuthorization($roles, $action)
    {
        $name   = $action;
        $action = $this->actions->search($name);

        if (is_null($action)) {
            throw new InvalidArgumentException("Unable to verify unknown action {$name}.");
        }

        $authorized = false;

        $roles = new Collection($roles);

        foreach ($roles->all() as $role) {
            $role       = $this->roles->search($role);
            $permission = isset($this->acl[$role.':'.$action]) ? $this->acl[$role.':'.$action] : false;

            // array_search() will return false when no key is found based
            // on given haystack, therefore we should just ignore and
            // continue to the next role.
            if (! is_null($role) && $permission === true) {
                $authorized = true;
            }
        }

        return $authorized;
    }

    /**
     * Assign single or multiple $roles + $actions to have access.
     *
     * @param  string|array  $roles      A string or an array of roles
     * @param  string|array  $actions    A string or an array of action name
     * @param  bool          $allow
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function setAuthorization($roles, $actions, $allow = true)
    {
        $roles   = $this->roles->filter($roles);
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
     * @param  array   $actions
     * @param  bool    $allow
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    protected function groupedAssignAction($role, array $actions, $allow = true)
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
     *
     * @param  string  $role       A key or string representation of roles
     * @param  string  $action     A key or string representation of action name
     * @param  bool    $allow
     *
     * @return void
     */
    protected function assign($role = null, $action = null, $allow = true)
    {
        $role   = $this->roles->findKey($role);
        $action = $this->actions->findKey($action);

        if (! is_null($role) && ! is_null($action)) {
            $this->acl["{$role}:{$action}"] = $allow;
        }
    }

    /**
     * Assign user instance.
     *
     * @param  \Orchestra\Contracts\Authorization\Authorizable  $user
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
     *
     * @return array
     */
    public function acl()
    {
        return $this->acl;
    }

    /**
     * Get the auth implementation.
     *
     * @return \Orchestra\Contracts\Auth\Guard
     */
    public function auth()
    {
        return $this->auth;
    }

    /**
     * Get the `actions` instance.
     *
     * @return \Orchestra\Authorization\Fluent
     */
    public function actions()
    {
        return $this->actions;
    }

    /**
     * Get the `roles` instance.
     *
     * @return \Orchestra\Authorization\Fluent
     */
    public function roles()
    {
        return $this->roles;
    }

    /**
     * Get all possible user roles.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getUserRoles()
    {
        if (! is_null($this->userRoles)) {
            return $this->userRoles;
        } elseif (! $this->auth->guest()) {
            return $this->auth->roles();
        }

        return new Collection($this->roles->has('guest') ? ['guest'] : []);
    }
}
