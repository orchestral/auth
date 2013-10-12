<?php namespace Orchestra\Auth;

use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['orchestra.commands.auth'] = $this->app->share(function()
		{
			return new Console\AuthCommand;
		});

		$this->commands('orchestra.commands.auth');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('orchestra.commands.auth');
	}
}
