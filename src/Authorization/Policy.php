<?php

namespace Orchestra\Authorization;

use Orchestra\Contracts\Authorization\Factory as FactoryContract;

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
    protected function getAuthorization()
    {
        return $this->acl;
    }

    /**
     * Resolve if authorization can.
     *
     * @param  string  $action
     *
     * @return bool
     */
    protected function can($action)
    {
        return $this->acl->can($action);
    }

    /**
     * Resolve if authorization can if action exists.
     *
     * @param  string  $action
     *
     * @return bool
     */
    protected function canIf($action)
    {
        return $this->acl->canIf($action);
    }

    /**
     * Get authorization driver name.
     *
     * @return string
     */
    protected function getAuthorizationName()
    {
        return $this->name ?: 'orchestra';
    }
}
