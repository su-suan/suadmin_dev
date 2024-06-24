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

namespace SuAdmin\Middlewares;

use Hyperf\Codec\Json;
use Hyperf\Context\Context;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\CoreMiddleware;
use SuAdmin\Annotation\DependProxy;
use SuAdmin\Utils\ApiCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[DependProxy(values: [CoreMiddleware::class])]
class HttpCoreMiddleware extends CoreMiddleware
{
    /** 跨域 header
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = Context::get(ResponseInterface::class);
        $response = su_admin_http_header($response);
        Context::set(ResponseInterface::class, $response);

        if ($request->getMethod() == 'OPTIONS') {
            return $response;
        }

        return parent::process($request, $handler);
    }

    /**
     * 在找不到任何路由时处理响应
     */
    protected function handleNotFound(ServerRequestInterface $request): ResponseInterface
    {
        $format = [
            'success' => false,
            'code' => ApiCode::NOT_FOUND,
            'message' => su_admin_translator('su_admin.not_found'),
        ];
        return $this->response()->withHeader('Server', 'SuAdmin')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withStatus(404)
            ->withBody(new SwooleStream(Json::encode($format)));
    }

    /**
     * 当找到路由但与任何可用方法不匹配时，处理响应
     */
    protected function handleMethodNotAllowed(
        array $methods,
        ServerRequestInterface $request
    ): ResponseInterface {
        $format = [
            'success' => false,
            'code' => ApiCode::METHOD_NOT_ALLOW,
            'message' => su_admin_translator('SuAdmin.allow_method', ['method' => implode(',', $methods)]),
        ];
        return $this->response()->withHeader('Server', 'SuAdmin')
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withStatus(405)
            ->withBody(new SwooleStream(Json::encode($format)));
    }
}
