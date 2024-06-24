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

use Hyperf\Database\Model\Builder;

interface QueryResource
{
    /**
     * 获取Query.
     */
    public function getQuery(): Builder;

    /**
     * 处理请求
     */
    public function handleSearch(Builder $query, array $params = [], array $extras = []): Builder;
}
