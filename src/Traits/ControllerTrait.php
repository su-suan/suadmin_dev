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

namespace SuAdmin\Traits;

use SuAdmin\SuAdminRequest;
use SuAdmin\SuAdminResponse;
use Psr\Http\Message\ResponseInterface;

trait ControllerTrait
{
    abstract public function getRequest(): SuAdminRequest;

    abstract public function getResponse(): SuAdminResponse;

    /** 成功返回
     * @param array|object|string|null $msgOrData
     * @param array|object $data
     * @param int $code
     * @return ResponseInterface
     */
    public function success(null|array|object|string $msgOrData = '', array|object $data = [], int $code = 200): ResponseInterface
    {
        if (is_string($msgOrData) || is_null($msgOrData)) {
            return $this->getResponse()->success($msgOrData, $data, $code);
        }
        if (is_array($msgOrData) || is_object($msgOrData)) {
            return $this->getResponse()->success(null, $msgOrData, $code);
        }
        return $this->getResponse()->success(null, $data, $code);
    }

    /** 失败返回
     * @param string $message
     * @param int $code
     * @param array $data
     * @return ResponseInterface
     */
    public function error(string $message = '', int $code = 500, array $data = []): ResponseInterface
    {
        return $this->getResponse()->error($message, $code, $data);
    }

    /** 重定向跳转
     * @param string $toUrl
     * @param int $status
     * @param string $schema
     * @return ResponseInterface
     */
    public function redirect(string $toUrl, int $status = 302, string $schema = 'http'): ResponseInterface
    {
        return $this->getResponse()->redirect($toUrl, $status, $schema);
    }

    /** 下载文件
     * @param string $filePath
     * @param string $name
     * @return ResponseInterface
     */
    public function _download(string $filePath, string $name = ''): ResponseInterface
    {
        return $this->getResponse()->download($filePath, $name);
    }
}
