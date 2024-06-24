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

namespace SuAdmin\Crontab;

class SuAdminCrontabScheduler
{
    /**
     * SuAdminCrontabManage.
     */
    protected SuAdminCrontabManage $crontabManager;

    /**
     * \SplQueue.
     */
    protected \SplQueue $schedules;

    /**
     * SuAdminCrontabScheduler constructor.
     */
    public function __construct(SuAdminCrontabManage $crontabManager)
    {
        $this->schedules = new \SplQueue();
        $this->crontabManager = $crontabManager;
    }

    public function schedule(): \SplQueue
    {
        foreach ($this->getSchedules() ?? [] as $schedule) {
            $this->schedules->enqueue($schedule);
        }
        return $this->schedules;
    }

    protected function getSchedules(): array
    {
        return $this->crontabManager->getCrontabList();
    }
}
