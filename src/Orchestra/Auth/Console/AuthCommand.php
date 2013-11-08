<?php namespace Orchestra\Auth\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class AuthCommand extends Command
{
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
    protected $description = 'Orchestra\Auth Command';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $action = $this->argument('action');

        if (in_array($action, array('install', 'upgrade'))) {
            $this->fireMigration();
            $this->info('orchestra/auth has been migrated');
        } else {
            $this->error("Invalid action [{$action}].");
        }
    }

    /**
     * Fire migration process.
     *
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
            array('action', InputArgument::REQUIRED, "Type of action, e.g: 'install', 'upgrade'."),
        );
    }
}
