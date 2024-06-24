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
use SuAdmin\Annotation\Role;
use SuAdmin\Exception\NoPermissionException;
use SuAdmin\Utils\Excel\Storage\LoginUser;
use SuAdmin\Interfaces\ServiceInterface\RoleServiceInterface;
use SuAdmin\Interfaces\ServiceInterface\UserServiceInterface;
use SuAdmin\SuAdminRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class RoleAspect.
 */
#[Aspect]
class RoleAspect extends AbstractAspect
{
    public array $annotations = [
        Role::class,
    ];

    /**
     * UserServiceInterface.
     */
    protected UserServiceInterface $service;

    /**
     * SuAdminRequest.
     */
    protected SuAdminRequest $request;

    /**
     * JWTAuth.
     */
    protected LoginUser $loginUser;

    /**
     * RoleAspect constructor.
     */
    public function __construct(
        UserServiceInterface $service,
        SuAdminRequest       $request,
        LoginUser            $loginUser
    ) {
        $this->service = $service;
        $this->request = $request;
        $this->loginUser = $loginUser;
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // 是超管角色放行
        if ($this->loginUser->isAdminRole()) {
            return $proceedingJoinPoint->process();
        }

        /* @var Role $role */
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Role::class])) {
            $role = $proceedingJoinPoint->getAnnotationMetadata()->method[Role::class];
        }

        // 没有使用注解，则放行
        if (empty($role->code)) {
            return $proceedingJoinPoint->process();
        }

        $this->checkRole($role->code, $role->where);

        return $proceedingJoinPoint->process();
    }

    /**
     * 检查角色.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function checkRole(array $roles,string $codeString, string $where): bool
    {
        if ($where === 'OR') {
            foreach (explode(',', $codeString) as $code) {
                if (in_array(trim($code), $roles)) {
                    return true;
                }
            }
            throw new NoPermissionException(
                su_admin_translator('system.no_role') . ' -> [ ' . $codeString . ' ]'
            );
        }

        if ($where === 'AND') {
            foreach (explode(',', $codeString) as $code) {
                $code = trim($code);
                if (! in_array($code, $roles)) {
                    $service = su_admin_container()->get(RoleServiceInterface::class);
                    throw new NoPermissionException(
                        su_admin_translator('system.no_role') . ' -> [ ' . $service->findNameByCode($code) . ' ]'
                    );
                }
            }
        }

        return true;
    }
}
