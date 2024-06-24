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

namespace SuAdmin\service;

use SuAdmin\Annotation\ComponentCollector;
use SuAdmin\Annotation\DependProxyCollector;
use SuAdmin\Listener\DependProxyListener;

class ServiceConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'listeners' => [
                DependProxyListener::class => PHP_INT_MAX,
            ],
            // 合并到  config/autoload/annotations.php 文件
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                    'collectors' => [
                        DependProxyCollector::class,
                        ComponentCollector::class,
                    ],
                ],
            ],
        ];
    }
}
