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

namespace SuAdmin;

use SuAdmin\Traits\ControllerTrait;

/**
 * API接口控制器基类
 * Class SuAdminApi.
 */
abstract class SuAdminApi
{
    use ControllerTrait;

    public function __construct(
        readonly protected SuAdminRequest $request,
        readonly protected SuAdminResponse $response
    ) {}

    public function getResponse(): SuAdminResponse
    {
        return $this->response;
    }

    public function getRequest(): SuAdminRequest
    {
        return $this->request;
    }
}
