<?php namespace Orchestra\Auth;

use \Illuminate\Auth\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['auth'] = $this->app->share(function($app)
		{
			// Once the authentication service has actually been requested by the developer
			// we will set a variable in the application indicating such. This helps us
			// know that we need to set any queued cookies in the after event later.
			$app['auth.loaded'] = true;

			return new AuthManager($app);
		});

		$this->app['orchestra.acl'] = $this->app->share(function($app)
		{
			return new Acl\Environment;
		});

		$this->registerAuthEvent();
	}

	/**
	 * We need to ensure that Orchestra\Acl is compliance with our Eloquent 
	 * Model, This would overwrite the default configuration.
	 *
	 * @retun  void
	 */
	protected function registerAuthEvent()
	{
		$this->app['events']->listen('orchestra.auth: roles', function ($user, $roles)
		{
			// Check if user is null, where roles wouldn't be available,
			// returning null would allow any other event listener (if any).
			if (is_null($user)) return ;

			foreach ($user->roles()->get() as $role)
			{
				array_push($roles, $role->name);
			}

			return $roles;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('auth', 'orchestra.acl');
	}
}
