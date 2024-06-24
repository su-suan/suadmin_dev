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

use Hyperf\Crontab\Crontab;

class SuAdminCrontab extends Crontab
{
    /**
     * 失败策略.
     */
    protected string $failPolicy = '3';

    /**
     * 调用参数.
     */
    protected string $parameter;

    /**
     * 任务ID.
     */
    protected int $crontab_id;

    public function getFailPolicy(): string
    {
        return $this->failPolicy;
    }

    public function setFailPolicy(string $failPolicy): void
    {
        $this->failPolicy = $failPolicy;
    }

    public function getParameter(): string
    {
        return $this->parameter;
    }

    public function setParameter(string $parameter): void
    {
        $this->parameter = $parameter;
    }

    public function getCrontabId(): int
    {
        return $this->crontab_id;
    }

    public function setCrontabId(int $crontab_id): void
    {
        $this->crontab_id = $crontab_id;
    }
}
