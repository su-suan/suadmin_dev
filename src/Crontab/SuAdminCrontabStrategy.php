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

use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Swoole\Coroutine;

use function Hyperf\Coroutine\co;

class SuAdminCrontabStrategy
{
    /**
     * SuAdminCrontabManage.
     */
    #[Inject]
    protected SuAdminCrontabManage $suAdminCrontabManage;

    /**
     * SuAdminExecutor.
     */
    #[Inject]
    protected SuAdminExecutor $executor;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function dispatch(SuAdminCrontab $crontab)
    {
        co(function () use ($crontab) {
            if ($crontab->getExecuteTime() instanceof Carbon) {
                $wait = $crontab->getExecuteTime()->getTimestamp() - time();
                $wait > 0 && Coroutine::sleep($wait);
                $this->executor->execute($crontab);
            }
        });
    }

    /**
     * 执行一次
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function executeOnce(SuAdminCrontab $crontab)
    {
        co(function () use ($crontab) {
            $this->executor->execute($crontab);
        });
    }
}
