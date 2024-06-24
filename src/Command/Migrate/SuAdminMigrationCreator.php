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

use Hyperf\Database\Migrations\MigrationCreator;

class SuAdminMigrationCreator extends MigrationCreator
{
    public function stubPath(): string
    {
        return BASE_PATH . '/vendor/suadmin/src/Command/Migrate/Stubs';
    }
}
