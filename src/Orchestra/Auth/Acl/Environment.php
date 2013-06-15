<?php namespace Orchestra\Auth\Acl;

use Orchestra\Auth\Guard;
use Orchestra\Support\Str;
use Orchestra\Memory\Drivers\Driver as MemoryDriver;

class Environment {

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
	 * @access public				
	 * @param  \Illuminate\Auth\Guard   $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Initiate a new ACL Container instance.
	 * 
	 * @access public
	 * @param  string                           $name
	 * @param  \Orchestra\Memory\Drivers\Driver $memory
	 * @return \Orchestra\Auth\Acl\Container
	 */
	public function make($name = null, MemoryDriver $memory = null)
	{
		if (is_null($name)) $name = 'default';

		if ( ! isset($this->drivers[$name]))
		{
			$this->drivers[$name] = new Container($this->auth, $name, $memory);
		}

		return $this->drivers[$name];
	}

	/**
	 * Register an ACL Container instance with Closure.
	 * 
	 * @access public
	 * @param  string   $name
	 * @param  Closure  $callback
	 * @return \Orchestra\Auth\Acl\Container
	 */
	public function register($name, $callback = null)
	{
		if (is_callable($name))
		{
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
	 * @access public
	 * @param  string   $method
	 * @param  array    $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		$result = array();
		$method = Str::snake($method, '_');

		if (preg_match('/^(add|fill|rename|has|get|remove)_(role)(s?)$/', $method, $matches))
		{
			$operation = $matches[1];
			$type      = $matches[2].'s';
			$multiple  = (isset($matches[3]) and $matches[3] === 's' and $operation === 'add');

			( !! $multiple) and $operation = 'fill';

			foreach ($this->drivers as $acl)
			{
				$result[] = $acl->execute($type, $operation, $parameters);

				if ('has' !== $operation) $acl->sync();
			}
		}

		return $result;
	}

	/**
	 * Shutdown Orchestra\Support\Acl.
	 *
	 * @access public
	 * @return void
	 */
	public function finish()
	{
		// Re-sync before shutting down.
		foreach($this->drivers as $acl) $acl->sync();

		$this->drivers = array();
	}

	/**
	 * Get all Orchestra\Support\Acl instances.
	 *
	 * @access public
	 * @return array
	 */
	public function all()
	{
		return $this->drivers;
	}
}
