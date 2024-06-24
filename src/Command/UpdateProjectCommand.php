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

namespace SuAdmin\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Database\Migrations\Migrator;
use Hyperf\Database\Seeders\Seed;
use SuAdmin\SuAdmin;
use SuAdmin\SuAdminCommand;

use function Hyperf\Support\make;

/**
 * Class UpdateProjectCommand.
 */
#[Command]
class UpdateProjectCommand extends SuAdminCommand
{
    /**
     * 更新项目命令.
     */
    protected ?string $name = 'suadmin:update';

    protected array $database = [];

    protected Seed $seed;

    protected Migrator $migrator;

    /**
     * UpdateProjectCommand constructor.
     */
    public function __construct(Migrator $migrator, Seed $seed)
    {
        parent::__construct();
        $this->migrator = $migrator;
        $this->seed = $seed;
    }

    public function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php suadmin:update" Update SuAdmin system');
        $this->setDescription('SuAdmin system update command');
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        $modules = make(SuAdmin::class)->getModuleInfo();
        $basePath = BASE_PATH . '/app/';
        $this->migrator->setConnection('default');

        foreach ($modules as $name => $module) {
            $seedPath = $basePath . $name . '/Database/Seeders/Update';
            $migratePath = $basePath . $name . '/Database/Migrations/Update';

            if (is_dir($migratePath)) {
                $this->migrator->run([$migratePath]);
            }

            if (is_dir($seedPath)) {
                $this->seed->run([$seedPath]);
            }
        }

        su_admin_redis_instance()->flushDB();

        $this->line($this->getGreenText('updated successfully...'));
    }
}
