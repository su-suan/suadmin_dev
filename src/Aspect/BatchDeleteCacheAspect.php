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

use Hyperf\Config\Annotation\Value;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use RedisException;
use SuAdmin\Annotation\BatchDeleteCache;
use SuAdmin\Utils\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class BatchDeleteCacheAspect.
 */
#[Aspect]
class BatchDeleteCacheAspect extends AbstractAspect
{
    public array $annotations = [
        BatchDeleteCache::class,
    ];

    /**
     * 缓存前缀
     */
    #[Value('cache.default.prefix')]
    protected string $prefix;

    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint): mixed
    {
        $suAdminRedis = su_admin_redis_instance();

        /** @var BatchDeleteCache $batchDeleteCache */
        $batchDeleteCache = $proceedingJoinPoint->getAnnotationMetadata()->method[BatchDeleteCache::class];

        $result = $proceedingJoinPoint->process();

        if (! empty($batchDeleteCache->keys)) {
            $keys = explode(',', $batchDeleteCache->keys);
            $iterator = null;
            $n = [];
            foreach ($keys as $key) {
                if (! Str::contains($key, '*')) {
                    $n[] = $this->prefix . $key;
                } else {
                    while (false !== ($k = $suAdminRedis->scan($iterator, $this->prefix . $key, 100))) {
                        $suAdminRedis->del($k);
                    }
                }
            }
            $suAdminRedis->del($n);
        }

        return $result;
    }
}
