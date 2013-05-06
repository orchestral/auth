<?php namespace Orchestra\Auth\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AuthCommand extends Command {
	
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'orchestra:auth';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Orchestra\Auth commandline tool';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		switch ($action = $this->argument('action'))
		{
			case 'install' :
			case 'upgrade' :
				$this->fireMigration();
				$this->info('orchestra/auth has been migrated');
				break;
			default :
				$this->error("Invalid action [{$action}].");
		}
	}

	/**
	 * Fire migration process.
	 *
	 * @access protected
	 * @return void
	 */
	protected function fireMigration()
	{
		$this->call('migrate', array('--package' => 'orchestra/auth'));
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('action', InputArgument::REQUIRED, "Type of action."),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}
}
