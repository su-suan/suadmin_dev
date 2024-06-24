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

namespace SuAdmin\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 删除缓存。
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class BatchDeleteCache extends AbstractAnnotation
{
    /**
     * @param null|string $keys 缓存key，多个以逗号分开
     */
    public function __construct(public ?string $keys = null) {}
}
