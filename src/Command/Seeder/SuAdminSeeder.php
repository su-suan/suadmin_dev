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

namespace SuAdmin\Command\Seeder;

use Hyperf\Command\Annotation\Command;
use Hyperf\Database\Commands\Seeders\BaseCommand;
use Hyperf\Database\Seeders\SeederCreator;
use Hyperf\Stringable\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class SuAdminSeeder.
 */
#[Command]
class SuAdminSeeder extends BaseCommand
{
    /**
     * The seeder creator instance.
     */
    protected SeederCreator $creator;

    protected string $module;

    /**
     * Create a new seeder generator command instance.
     */
    public function __construct(SeederCreator $creator)
    {
        parent::__construct('suadmin:seeder-gen');
        $this->setDescription('Generate a new SuAdmin module seeder class');

        $this->creator = $creator;
    }

    /**
     * Handle the current command.
     */
    public function handle()
    {
        $this->module = ucfirst(trim($this->input->getOption('module')));
        $name = Str::snake(trim($this->input->getArgument('name')));

        $this->writeMigration($name);
    }

    /**
     * Write the seeder file to disk.
     */
    protected function writeMigration(string $name)
    {
        $path = $this->ensureSeederDirectoryAlreadyExist(
            $this->getSeederPath()
        );

        $file = pathinfo($this->creator->create($name, $path), PATHINFO_FILENAME);

        $this->info("<info>[INFO] Created Seeder:</info> {$file}");
    }

    protected function ensureSeederDirectoryAlreadyExist(string $path): string
    {
        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }

        return $path;
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the seeder'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['module', null, InputOption::VALUE_REQUIRED, 'Please enter the module to be generated'],
            ['path', null, InputOption::VALUE_OPTIONAL, 'The location where the seeder file should be created'],
            ['realpath', null, InputOption::VALUE_NONE, 'Indicate any provided seeder file paths are pre-resolved absolute paths'],
        ];
    }

    protected function getSeederPath(): string
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return ! $this->usingRealPath()
                ? BASE_PATH . '/' . $targetPath
                : $targetPath;
        }

        return BASE_PATH . '/app/' . ucfirst($this->module) . '/Database/Seeders';
    }
}
