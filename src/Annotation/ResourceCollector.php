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

use Hyperf\Di\MetadataCollector;

class ResourceCollector extends MetadataCollector
{
    protected static array $container = [];

    public static function collectClass(string $className, string $tag)
    {
        static::$container['resource'][$tag] = $className;
    }

    public static function getByCodeService(string $tag)
    {
        $class = static::$container['resource'][$tag] ?? null;
        if (empty($class)) {
            throw new \RuntimeException(sprintf('没有找到%s,或者类[%s]没有实现ResourceService契约', $tag, $class ?? 'null'));
        }
        return su_admin_container()->get($class);
    }

    public static function list(): array
    {
        return parent::list()['resource'] ?? []; // TODO: Change the autogenerated stub
    }
}
