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

namespace SuAdmin\Interfaces;

interface SuAdminRedisInterface
{
    /**
     * 设置 key 类型名.
     */
    public function setTypeName(string $typeName): void;

    /**
     * 获取key 类型名.
     */
    public function getTypeName(): string;
}
