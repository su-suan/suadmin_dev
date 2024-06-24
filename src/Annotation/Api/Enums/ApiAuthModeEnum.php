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

namespace SuAdmin\Annotation\Api\Enums;

enum ApiAuthModeEnum: int
{
    /**
     * 简单模式.
     */
    case EASY = 1;

    /**
     * 复杂模式.
     */
    case NORMAL = 2;
}
