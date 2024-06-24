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

#[\Attribute(\Attribute::TARGET_METHOD)]
class Api extends AbstractAnnotation
{
    public const AUTH_MODE_EASY = 1;

    public const AUTH_MODE_NORMAL = 2;

    public const METHOD_ALL = 'A';

    public const METHOD_POST = 'P';

    public const METHOD_GET = 'G';

    public const METHOD_PUT = 'U';

    public const METHOD_DELETE = 'D';

    public function __construct(
        // 访问名
        public string $accessName,
        // 接口名
        public string $name,
        // 描述信息
        public string $description,
        // appId
        public array|string $appId,
        // 是否启用
        public int $status = 1,
        // 验证模式 1 简单  2 复杂;
        public int $authMode = self::AUTH_MODE_EASY,
        // 请求方式 A, P, G, U, D
        public string $requestMode = self::METHOD_ALL,
        // api的所属分组id
        public int $groupId = 0,
        // 备注
        public string $remark = '',
        // 响应示例
        public string $response = "{\n  code: 200,\n  success: true,\n  message: '请求成功',\n  data: []\n}"
    ) {}
}
