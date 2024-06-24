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

interface TaskMutex
{
    /**
     * Attempt to obtain a task mutex for the given crontab.
     */
    public function create(SuAdminCrontab $crontab): bool;

    /**
     * Determine if a task mutex exists for the given crontab.
     */
    public function exists(SuAdminCrontab $crontab): bool;

    /**
     * Clear the task mutex for the given crontab.
     */
    public function remove(SuAdminCrontab $crontab);
}
