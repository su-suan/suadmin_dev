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
 * 依赖代理注解，用于平替某个类.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class DependProxy extends AbstractAnnotation
{
    public function __construct(public array $values = [], public ?string $provider = null) {}

    public function collectClass(string $className): void
    {
        if (! $this->provider) {
            $this->provider = $className;
        }
        if (count($this->values) == 0 && class_exists($className)) {
            $reflection = new \ReflectionClass($className);
            $interfaces = $reflection->getInterfaces();
            // 按照定义顺序排序接口列表
            uasort($interfaces, function ($a, $b) {
                if (in_array($a->getName(), class_implements($b->getName()))) {
                    return 1;
                }
                if (in_array($b->getName(), class_implements($a->getName()))) {
                    return -1;
                }
                return 0;
            });
            $this->values = [array_values($interfaces)[0]->getName()];
        }
        DependProxyCollector::setAround($className, $this);
    }
}
