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

use Hyperf\Context\Context;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use SuAdmin\SuAdminModel;
use SuAdmin\SuAdminRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;

use function Hyperf\Config\config;

/**
 * Class UpdateAspect.
 */
#[Aspect]
class UpdateAspect extends AbstractAspect
{
    public array $classes = [
        'SuAdmin\SuAdminModel::update',
    ];

    /**
     * @return mixed
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $instance = $proceedingJoinPoint->getInstance();
        // 更新更改人
        if ($instance instanceof SuAdminModel
            && in_array('updated_by', $instance->getFillable())
            && config('SuAdmin.data_scope_enabled')
            && Context::has(ServerRequestInterface::class)
            && su_admin_container()->get(SuAdminRequest::class)->getHeaderLine('authorization')
        ) {
            try {
                $instance->updated_by = user()->getId();
            } catch (\Throwable $e) {
            }
        }
        return $proceedingJoinPoint->process();
    }
}
