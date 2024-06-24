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

namespace SuAdmin\Interfaces\ServiceInterface;

use RedisException;

interface ConfigServiceInterface
{
    /**
     * 按key获取配置，并缓存.
     * @throws RedisException
     */
    public function getConfigByKey(string $key): ?array;
}
