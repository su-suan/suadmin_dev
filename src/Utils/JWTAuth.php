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

namespace SuAdmin\Utils;

use SuAdmin\Exception\TokenException;
use SuAdmin\Interfaces\ServiceInterface\RoleServiceInterface;
use SuAdmin\Interfaces\ServiceInterface\UserServiceInterface;
use SuAdmin\SuAdminRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

use function Hyperf\Support\env;
use function Hyperf\Support\make;

class JWTAuth
{
    protected JWT $jwt;

    protected SuAdminRequest $request;

    /**
     * JWTAuth constructor.
     * @param string $scene 场景，默认为default
     */
    public function __construct(string $scene = 'default')
    {
        /* @var JWT $this->jwt */
        $this->jwt = make(JWT::class)->setScene($scene);
    }

    /**
     * 验证token.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function check(?string $token = null, string $scene = 'default'): bool
    {
        try {
            if ($this->jwt->checkToken($token, $scene, true, true, true)) {
                return true;
            }
        } catch (InvalidArgumentException $e) {
            throw new TokenException(su_admin_translator('jwt.no_token'));
        } catch (\Throwable $e) {
            throw new TokenException(su_admin_translator('jwt.no_login'));
        }

        return false;
    }

    /**
     * 获取JWT对象
     */
    public function getJwt(): JWT
    {
        return $this->jwt;
    }

    /**
     * 获取当前登录用户信息.
     */
    public function getUserInfo(?string $token = null): array
    {
        return $this->jwt->getParserData($token);
    }

    /**
     * 获取当前登录用户ID.
     */
    public function getId(): int
    {
        return $this->jwt->getParserData()['id'];
    }

    /**
     * 获取当前登录用户名.
     */
    public function getUsername(): string
    {
        return $this->jwt->getParserData()['username'];
    }

    /**
     * 获取当前登录用户角色.
     */
    public function getUserRole(array $columns = ['id', 'name', 'code']): array
    {
        return su_admin_container()->get(UserServiceInterface::class)->read($this->getId(), ['id'])->roles()->get($columns)->toArray();
    }

    /**
     * 获取当前登录用户岗位.
     */
    public function getUserPost(array $columns = ['id', 'name', 'code']): array
    {
        return su_admin_container()->get(UserServiceInterface::class)->read($this->getId(), ['id'])->posts()->get($columns)->toArray();
    }

    /**
     * 获取当前登录用户部门.
     */
    public function getUserDept(array $columns = ['id', 'name']): array
    {
        return su_admin_container()->get(UserServiceInterface::class)->read($this->getId(), ['id'])->depts()->get($columns)->toArray();
    }

    /**
     * 获取当前token用户类型.
     */
    public function getUserType(): string
    {
        return $this->jwt->getParserData()['user_type'];
    }

    /**
     * 是否为超级管理员（创始人），用户禁用对创始人没用.
     */
    public function isSuperAdmin(): bool
    {
        return env('SUPER_ADMIN') == $this->getId();
    }

    /**
     * 是否为管理员角色.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function isAdminRole(): bool
    {
        return in_array(
            su_admin_container()->get(RoleServiceInterface::class)->read((int) env('ADMIN_ROLE'), ['code'])->code,
            su_admin_container()->get(UserServiceInterface::class)->getInfo()['roles']
        );
    }

    /**
     * 获取Token.
     * @throws InvalidArgumentException
     */
    public function getToken(array $user): string
    {
        return $this->jwt->getToken($user);
    }

    /**
     * 刷新token.
     * @throws InvalidArgumentException
     */
    public function refresh(): string
    {
        return $this->jwt->refreshToken();
    }
}
