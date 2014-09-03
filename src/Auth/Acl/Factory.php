<?php namespace Orchestra\Auth\Acl;

use Illuminate\Support\Arr;
use Orchestra\Auth\Guard;
use Orchestra\Memory\Provider;

class Factory
{
    /**
     * Auth instance.
     *
     * @var \Illuminate\Auth\Guard
     */
    protected $auth;

    /**
     * Cache ACL instance so we can reuse it on multiple request.
     *
     * @var array
     */
    protected $drivers = array();

    /**
     * Construct a new Environment.
     *
     * @param  \Orchestra\Auth\Guard    $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Initiate a new ACL Container instance.
     *
     * @param  string                      $name
     * @param  \Orchestra\Memory\Provider  $memory
     * @return \Orchestra\Auth\Acl\Container
     */
    public function make($name = null, Provider $memory = null)
    {
        if (is_null($name)) {
            $name = 'default';
        }

        if (! isset($this->drivers[$name])) {
            $this->drivers[$name] = new Container($this->auth, $name, $memory);
        }

        return $this->drivers[$name];
    }

    /**
     * Register an ACL Container instance with Closure.
     *
     * @param  string   $name
     * @param  \Closure $callback
     * @return \Orchestra\Auth\Acl\Container
     */
    public function register($name, $callback = null)
    {
        if (is_callable($name)) {
            $callback = $name;
            $name     = null;
        }

        $instance = $this->make($name);

        call_user_func($callback, $instance);

        return $instance;
    }

    /**
     * Manipulate and synchronize roles.
     *
     * @param  string   $method
     * @param  array    $parameters
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        $response = array();

        foreach ($this->drivers as $acl) {
            $response[] = call_user_func_array(array($acl, $method), $parameters);
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

        $this->drivers = array();

        return $this;
    }

    /**
     * Get all ACL instances.
     *
     * @return array
     */
    public function all()
    {
        return $this->drivers;
    }

    /**
     * Get ACL instance by name.
     *
     * @param  string   $name
     * @return \Orchestra\Auth\Acl\Container
     */
    public function get($name)
    {
        return Arr::get($this->drivers, $name);
    }
}
