<?php

declare(strict_types=1);
/**
 * This file is part of SuAdmin.
 *
 * @link     https://www.SuAdmin.com
 * @document https://doc.SuAdmin.com
 * @contact  yqhcode@qq.com
 * @license  https://github.com/su-suan/suadmin
 */

namespace SuAdmin\Command\Migrate;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Concerns\Confirmable;
use Hyperf\Database\Commands\Migrations\BaseCommand;
use Hyperf\Database\Migrations\Migrator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class SuAdminMigrateRun.
 */
#[Command]
class SuAdminMigrateRun extends BaseCommand
{
    use Confirmable;

    protected ?string $name = 'suadmin:migrate-run';

    /**
     * The console command description.
     */
    protected string $description = 'Run the database migrations';

    /**
     * The migrator instance.
     *
     * @var Migrator
     */
    protected $migrator;

    protected $module;

    /**
     * Create a new migration command instance.
     */
    public function __construct(Migrator $migrator)
    {
        parent::__construct();

        $this->migrator = $migrator;

        $this->setDescription('The run migrate class of SuAdmin module');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $this->module = trim($this->input->getArgument('name'));

        $this->prepareDatabase();

        // Next, we will check to see if a path option has been defined. If it has
        // we will use the path relative to the root of this installation folder
        // so that migrations may be run for any path within the applications.
        $this->migrator->setOutput($this->output)
            ->run($this->getMigrationPaths(), [
                'pretend' => $this->input->getOption('pretend'),
                'step' => $this->input->getOption('step'),
            ]);

        // Finally, if the "seed" option has been given, we will re-run the database
        // seed task to re-populate the database, which is convenient when adding
        // a migration and a seed at the same time, as it is only this command.
        if ($this->input->getOption('seed') && ! $this->input->getOption('pretend')) {
            $this->call('db:seed', ['--force' => true]);
        }
    }

    protected function getOptions(): array
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to the migrations files to be executed'],
            ['realpath', null, InputOption::VALUE_NONE, 'Indicate any provided migration file paths are pre-resolved absolute paths'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run'],
            ['step', null, InputOption::VALUE_NONE, 'Force the migrations to be run so they can be rolled back individually'],
        ];
    }

    /**
     * Prepare the migration database for running.
     */
    protected function prepareDatabase()
    {
        $this->migrator->setConnection($this->input->getOption('database') ?? 'default');

        if (! $this->migrator->repositoryExists()) {
            $this->call('migrate:install', array_filter([
                '--database' => $this->input->getOption('database'),
            ]));
        }
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'Please enter the module to be run'],
        ];
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     */
    protected function getMigrationPath(): string
    {
        return BASE_PATH . '/app/' . ucfirst($this->module) . '/Database/Migrations';
    }
}
