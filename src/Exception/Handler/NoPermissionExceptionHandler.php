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

namespace SuAdmin\Exception\Handler;

use Hyperf\Codec\Json;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use SuAdmin\Exception\NoPermissionException;
use SuAdmin\Utils\ApiCode;
use Psr\Http\Message\ResponseInterface;

/**
 * Class NoPermissionExceptionHandler.
 */
class NoPermissionExceptionHandler extends ExceptionHandler
{
    public function handle(\Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->stopPropagation();
        $format = [
            'success' => false,
            'message' => $throwable->getMessage(),
            'code' => ApiCode::NO_PERMISSION,
        ];
        $response = su_admin_http_header($response);
        return $response->withHeader('Server', 'SuAdmin')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withStatus(403)->withBody(new SwooleStream(Json::encode($format)));
    }

    public function isValid(\Throwable $throwable): bool
    {
        return $throwable instanceof NoPermissionException;
    }
}
