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

namespace SuAdmin\Interfaces\ServiceInterface\Resource;

interface DataResource
{
    /**
     * 获取数据.
     */
    public function data(array $params = [], array $extras = []): array;
}
