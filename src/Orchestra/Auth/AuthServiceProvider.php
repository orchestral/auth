<?php namespace Orchestra\Auth;

use Illuminate\Auth\AuthServiceProvider as ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * {@inheritdoc}
	 */
	public function register()
	{
		$this->registerAuth();
		$this->registerAcl();
		$this->registerAuthListener();
	}

	/**
	 * Register the service provider for Auth.
	 *
	 * @return void
	 */
	protected function registerAuth()
	{
		$this->app['auth'] = $this->app->share(function ($app) {
			// Once the authentication service has actually been requested by the developer
			// we will set a variable in the application indicating such. This helps us
			// know that we need to set any queued cookies in the after event later.
			$app['auth.loaded'] = true;

			return new AuthManager($app);
		});
	}

	/**
	 * Register the service provider for Acl.
	 *
	 * @return void
	 */
	protected function registerAcl()
	{
		$this->app['orchestra.acl'] = $this->app->share(function ($app) {
			return new Acl\Environment($app['auth']->driver());
		});

		$this->app->booting(function () {
			$loader = AliasLoader::getInstance();
			$loader->alias('Orchestra\Acl', 'Orchestra\Support\Facades\Acl');
		});
	}

	/**
	 * We need to ensure that Orchestra\Acl is compliance with our Eloquent
	 * Model, This would overwrite the default configuration.
	 *
	 * @return void
	 */
	protected function registerAuthListener()
	{
		$this->app['events']->listen('orchestra.auth: roles', function ($user, $roles) {
			// Check if user is null, where roles wouldn't be available,
			// returning null would allow any other event listener (if any).
			if (is_null($user)) {
				return ;
			}

			foreach ($user->roles()->get() as $role) {
				array_push($roles, $role->name);
			}

			return $roles;
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function boot()
	{
		$this->package('orchestra/auth', 'orchestra/auth');

		parent::boot();
	}

	/**
	 * {@inheritdoc}
	 */
	public function provides()
	{
		return array('auth', 'orchestra.acl');
	}
}
