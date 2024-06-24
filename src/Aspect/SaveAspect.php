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
use SuAdmin\SuAdminModel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function Hyperf\Config\config;

/**
 * Class SaveAspect.
 */
#[Aspect]
class SaveAspect extends AbstractAspect
{
    public array $classes = [
        'SuAdmin\SuAdminModel::save',
    ];

    /**
     * @return mixed
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /** @var SuAdminModel $instance */
        $instance = $proceedingJoinPoint->getInstance();

        if (config('SuAdmin.data_scope_enabled')) {
            try {
                $user = user();
                // 设置创建人
                if ($instance instanceof SuAdminModel
                    && in_array($instance->getDataScopeField(), $instance->getFillable())
                    && is_null($instance[$instance->getDataScopeField()])
                ) {
                    $user->check();
                    $instance[$instance->getDataScopeField()] = $user->getId();
                }

                // 设置更新人
                if ($instance instanceof SuAdminModel && in_array('updated_by', $instance->getFillable())) {
                    $user->check();
                    $instance->updated_by = $user->getId();
                }
            } catch (\Throwable $e) {
            }
        }
        // 生成雪花ID 或者 UUID
        if ($instance instanceof SuAdminModel
            && ! $instance->incrementing
            && empty($instance->{$instance->getKeyName()})
        ) {
            $instance->setPrimaryKeyValue($instance->getPrimaryKeyType() === 'int' ? su_admin_snowflake_id() : su_admin_uuid());
        }
        return $proceedingJoinPoint->process();
    }
}
