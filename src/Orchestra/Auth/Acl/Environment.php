<?php namespace Orchestra\Auth\Acl;

use Orchestra\Auth\Guard;
use Orchestra\Support\Str;
use Orchestra\Memory\Drivers\Driver as MemoryDriver;

class Environment
{
    /**
     * Auth instance.
     *
     * @var \Illuminate\Auth\Guard
     */
    protected $auth = null;

    /**
     * Cache ACL instance so we can reuse it on multiple request.
     *
     * @var array
     */
    protected $drivers = array();

    /**
     * Construct a new Environment.
     *
     * @param  \Illuminate\Auth\Guard   $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Initiate a new ACL Container instance.
     *
     * @param  string                           $name
     * @param  \Orchestra\Memory\Drivers\Driver $memory
     * @return Container
     */
    public function make($name = null, MemoryDriver $memory = null)
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
     * @return Container
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
    public function __call($method, $parameters)
    {
        $result = array();

        // Dynamically resolve operation name especially to resolve
        // attach and detach multiple actions or roles.
        $resolveOperation = function ($operation, $multiple) {
            if (! $multiple) {
                return $operation;
            } elseif (in_array($operation, array('fill', 'add'))) {
                return 'attach';
            }

            return 'detach';
        };

        $method = Str::snake($method, '_');
        $matcher = '/^(add|rename|has|get|remove|fill|attach|detach)_(role|action)(s?)$/';

        if (preg_match($matcher, $method, $matches)) {
            $type      = $matches[2].'s';
            $multiple  = (isset($matches[3]) and $matches[3] === 's');
            $operation = $resolveOperation($matches[1], $multiple);

            foreach ($this->drivers as $acl) {
                $result[] = $acl->execute($type, $operation, $parameters);

                if ('has' !== $operation) {
                    $acl->sync();
                }
            }
        }

        return $result;
    }

    /**
     * Shutdown Orchestra\Support\Acl.
     *
     * @return Environment
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
     * Get all Orchestra\Support\Acl instances.
     *
     * @return array
     */
    public function all()
    {
        return $this->drivers;
    }
}
