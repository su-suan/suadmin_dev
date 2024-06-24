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

use Closure;
use Hyperf\Coroutine\Coroutine;
use RedisException;
use SuAdmin\Abstracts\AbstractRedis;
use SuAdmin\Exception\NormalStatusException;
use SuAdmin\Interfaces\SuAdminRedisInterface;
use Throwable;
use function Hyperf\Support\call;

class RedisLock extends AbstractRedis implements SuAdminRedisInterface
{
    /** 设置 key 类型名
     * @param string $typeName
     * @return void
     */
    public function setTypeName(string $typeName): void
    {
        $this->typeName = $typeName;
    }

    /** 获取key 类型名
     * @return string
     */
    public function getTypeName(): string
    {
        return $this->typeName;
    }

    /** 运行锁
     * @param Closure $closure
     * @param string $key
     * @param int $expired
     * @param int $timeout
     * @param float $sleep
     * @return bool
     * @throws RedisException
     */
    public function run(Closure $closure, string $key, int $expired, int $timeout = 0, float $sleep = 0.1): bool
    {
        if (! $this->lock($key, $expired, $timeout, $sleep)) {
            return false;
        }

        /*
         * @phpstan-ignore-next-line
         */
        try {
            call($closure);
        } catch (Throwable $e) {
            $this->getLogger()->error(su_admin_translator('SuAdmin.redis_lock_error'), [$e->getMessage(), $e->getTrace()]);
            throw new NormalStatusException(su_admin_translator('SuAdmin.redis_lock_error'), 500);
        } finally {
            $this->freed($key);
        }

        return true;
    }

    /** 检查锁
     * @param string $key
     * @return bool
     * @throws RedisException
     */
    public function check(string $key): bool
    {
        return $this->getRedis()->exists($this->getKey($key));
    }

    /** 添加锁
     * @param string $key
     * @param int $expired
     * @param int $timeout
     * @param float $sleep
     * @return bool
     * @throws RedisException
     */
    public function lock(string $key, int $expired, int $timeout = 0, float $sleep = 0.1): bool
    {
        $retry = $timeout > 0 ? intdiv($timeout * 100, 10) : 1;

        $name = $this->getKey($key);

        while ($retry > 0) {
            $lock = $this->getRedis()->set($name, 1, ['nx', 'ex' => $expired]);
            if ($lock || $timeout === 0) {
                break;
            }
            Coroutine::id() ? Coroutine::sleep($sleep) : usleep(9999999);

            --$retry;
        }

        return true;
    }

    /** 释放锁
     * @param string $key
     * @return bool
     * @throws RedisException
     */
    public function freed(string $key): bool
    {
        $luaScript = <<<'Lua'
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return 0
            end
        Lua;

        return $this->getRedis()->eval($luaScript, [$this->getKey($key), 1], 1) > 0;
    }
}
