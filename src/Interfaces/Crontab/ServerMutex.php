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

namespace SuAdmin\Interfaces\Crontab;

use SuAdmin\Crontab\SuAdminCrontab;

interface ServerMutex
{
    /**
     * Attempt to obtain a server mutex for the given crontab.
     */
    public function attempt(SuAdminCrontab $crontab): bool;

    /**
     * Get the server mutex for the given crontab.
     */
    public function get(SuAdminCrontab $crontab): string;
}
