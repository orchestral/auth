<?php

namespace Orchestra\Authorization;

use Orchestra\Contracts\Authorization\Authorizable;
use Orchestra\Contracts\Authorization\Factory as FactoryContract;
use Orchestra\Contracts\Authorization\Authorization as AuthorizationContract;

abstract class Policy
{
    /**
     * The authorization implementation.
     *
     * @var \Orchestra\Contracts\Authorization\Authorization
     */
    protected $acl;

    /**
     * Authorization driver name.
     *
     * @var string
     */
    protected $name;

    /**
     * Set authorization driver.
     *
     * @param  \Orchestra\Contracts\Authorization\Factory  $factory
     *
     * @return $this
     */
    public function setAuthorization(FactoryContract $factory)
    {
        $this->acl = $factory->make($this->getAuthorizationName());

        return $this;
    }

    /**
     * Get authorization driver.
     *
     * @return \Orchestra\Contracts\Authorization\Authorization
     */
    protected function getAuthorization(): AuthorizationContract
    {
        return $this->acl;
    }

    /**
     * Resolve if authorization can.
     *
     * @param  string  $action
     * @param  \Orchestra\Contracts\Authorization\Authorizable|null  $user
     *
     * @return bool
     */
    protected function can(string $action, ?Authorizable $user = null): bool
    {
        return ! \is_null($user)
                    ? $this->acl->canAs($user, $action)
                    : $this->acl->can($action);
    }

    /**
     * Resolve if authorization can if action exists.
     *
     * @param  string  $action
     * @param  \Orchestra\Contracts\Authorization\Authorizable|null  $user
     *
     * @return bool
     */
    protected function canIf(string $action, ?Authorizable $user = null): bool
    {
        return ! \is_null($user)
                    ? $this->acl->canIfAs($user, $action)
                    : $this->acl->canIf($action);
    }

    /**
     * Get authorization driver name.
     *
     * @return string
     */
    protected function getAuthorizationName(): string
    {
        return $this->name ?? 'orchestra';
    }
}
