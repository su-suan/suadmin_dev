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

namespace SuAdmin\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Redis\Redis;
use SuAdmin\Annotation\Resubmit;
use SuAdmin\Exception\NormalStatusException;
use SuAdmin\Exception\SuAdminException;
use SuAdmin\SuAdminRequest;
use SuAdmin\Utils\Excel\Storage\RedisLock;
use function Hyperf\Support\make;

/**
 * Class ResubmitAspect.
 */
#[Aspect]
class ResubmitAspect extends AbstractAspect
{
    public array $annotations = [
        Resubmit::class,
    ];

    /**
     * @return mixed
     * @throws Exception
     * @throws \Throwable
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        try {
            /* @var $resubmit Resubmit */
            if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Resubmit::class])) {
                $resubmit = $proceedingJoinPoint->getAnnotationMetadata()->method[Resubmit::class];
            }

            $request = su_admin_container()->get(SuAdminRequest::class);

            $key = md5(sprintf('%s-%s-%s', $request->ip(), $request->getPathInfo(), $request->getMethod()));

            $lockRedis = new RedisLock(
                make(Redis::class),
                make(LoggerFactory::class)->get('SuAdmin Redis Lock')
            );
            $lockRedis->setTypeName('resubmit');

            if ($lockRedis->check($key)) {
                $lockRedis = null;
                throw new NormalStatusException($resubmit->message ?: su_admin_translator('SuAdmin.resubmit'), 500);
            }

            $lockRedis->lock($key, $resubmit->second);
            $lockRedis = null;

            return $proceedingJoinPoint->process();
        } catch (\Throwable $e) {
            throw new SuAdminException($e->getMessage(), $e->getCode());
        }
    }
}
