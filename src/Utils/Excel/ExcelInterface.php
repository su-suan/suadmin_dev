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

namespace SuAdmin\Utils\Excel;

use SuAdmin\SuAdminModel;
use Psr\Http\Message\ResponseInterface;

interface ExcelInterface
{
    public function import(SuAdminModel $model, ?\Closure $closure = null): bool;

    public function export(string $filename, array|\Closure $closure): ResponseInterface;
}
