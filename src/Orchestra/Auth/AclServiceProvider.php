<?php namespace Orchestra\Auth;

use Illuminate\Support\ServiceProvider;

class AclServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() 
	{
		$this->app['orchestra.acl'] = $this->app->share(function($app)
		{
			return new Acl\Environment;
		});
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('orchestra/auth', 'orchestra/auth');
	}
}