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

namespace SuAdmin\Annotation\Api;

use Hyperf\Di\Annotation\AbstractAnnotation;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ApiRequestParam extends AbstractAnnotation
{
    public function __construct(
        // 参数名称
        public string $name,
        // 参数描述
        public string $description,
        // 参数类型  String, Integer, Array, Float, Boolean, Enum, Object, File
        public string $dataType = 'String',
        // 默认值
        public string $defaultValue = '',
        // 是否必须填 1 非必填 2 必填
        public int $isRequired = 1,
        // 是否启用 1 启用 2 不启用
        public int $status = 1,
    ) {}

    public function collectMethod(string $className, ?string $target): void
    {
        ApiRequestParamCollector::collectMethod($className, $target, static::class, $this);
    }
}
