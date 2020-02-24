<?php

namespace Orchestra\Authorization;

use Illuminate\Contracts\Auth\Guard;
use Orchestra\Contracts\Authorization\Authorization as AuthorizationContract;
use Orchestra\Contracts\Authorization\Factory as FactoryContract;
use Orchestra\Contracts\Memory\Provider;

class Factory implements FactoryContract
{
    /**
     * Auth instance.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * Cache ACL instance so we can reuse it on multiple request.
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * Construct a new Environment.
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Initiate a new ACL Container instance.
     */
    public function make(string $name = null, ?Provider $memory = null): AuthorizationContract
    {
        $name = $name ?? 'default';

        if (! isset($this->drivers[$name])) {
            $this->drivers[$name] = (new Authorization($name, $memory))->setAuthenticator($this->auth);
        }

        return $this->drivers[$name];
    }

    /**
     * Register an ACL Container instance with Closure.
     *
     * @param  string|callable  $name
     */
    public function register($name, ?callable $callback = null): AuthorizationContract
    {
        if (\is_callable($name)) {
            $callback = $name;
            $name = null;
        }

        $instance = $this->make($name);

        $callback($instance);

        return $instance;
    }

    /**
     * Manipulate and synchronize roles.
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        $response = [];

        foreach ($this->drivers as $acl) {
            $response[] = $acl->{$method}(...$parameters);
        }

        return $response;
    }

    /**
     * Shutdown/finish all ACL.
     *
     * @return $this
     */
    public function finish()
    {
        // Re-sync before shutting down.
        foreach ($this->drivers as $acl) {
            $acl->sync();
        }

        $this->drivers = [];

        return $this;
    }

    /**
     * Get all ACL instances.
     */
    public function all(): array
    {
        return $this->drivers;
    }

    /**
     * Get ACL instance by name.
     */
    public function get(string $name): ?AuthorizationContract
    {
        return $this->drivers[$name] ?? null;
    }
}
