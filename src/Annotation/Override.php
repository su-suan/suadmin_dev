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
use SuAdmin\Exception\ServiceException;

#[\Attribute(\Attribute::TARGET_FUNCTION)]
class Override extends AbstractAnnotation
{
    public function collectMethod(string $className, ?string $target): void
    {
        $methodReflect = new \ReflectionMethod($className, $target);
        if (
            ! $methodReflect->isStatic()
            || ! $methodReflect->hasReturnType()
            || ! in_array((string) $methodReflect->getReturnType(), ['self', $className])
        ) {
            throw new ServiceException(
                $className . ' The override annotation is used on static methods and returns an instance of the current class'
            );
        }

        ComponentCollector::collectOverride($className, [$className, $target]);
    }
}
