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

#[\Attribute(\Attribute::TARGET_FUNCTION | \Attribute::IS_REPEATABLE)]
class PostConstruct extends AbstractAnnotation
{
    public function __construct(public int $order = 0) {}

    public function collectMethod(string $className, ?string $target): void
    {
        ComponentCollector::collectPostConstruct($className, $this->order, [
            $className, $target,
        ]);
    }
}
