<?php namespace Orchestra\Auth;

class AuthServiceProvider extends \Illuminate\Auth\AuthServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerAuthEvent();
		
		$this->app['auth'] = $this->app->share(function($app)
		{
			// Once the authentication service has actually been requested by the developer
			// we will set a variable in the application indicating such. This helps us
			// know that we need to set any queued cookies in the after event later.
			$app['auth.loaded'] = true;

			return new AuthManager($app);
		});
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
}