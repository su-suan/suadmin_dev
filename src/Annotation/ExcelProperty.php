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
 * excel导入导出元数据。
 * @Annotation
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ExcelProperty extends AbstractAnnotation
{
    /**
     * @param null|string $value 字段名
     * @param null|int $index 列索引号
     * @param null|int $width 宽度
     * @param null|string $align 文字对齐方式
     * @param null|string $headColor 表头字体颜色
     * @param null|string $headBgColor 表头背景颜色
     * @param null|string $color 表体文字颜色
     * @param null|string $bgColor 表体表格背景颜色
     * @param null|array $dictData 字典数据
     * @param null|string $dictName 字典名称
     * @param null|string $path 数据路径 用法: object.value
     */
    public function __construct(
        public ?string $value = null,
        public ?int $index = null,
        public ?int $width = null,
        public ?string $align = null,
        public ?string $headColor = null,
        public ?string $headBgColor = null,
        public ?string $color = null,
        public ?string $bgColor = null,
        public ?array $dictData = null,
        public ?string $dictName = null,
        public ?string $path = null
    ) {}
}
