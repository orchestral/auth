<?php namespace Orchestra\Auth;

use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider {

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
}
