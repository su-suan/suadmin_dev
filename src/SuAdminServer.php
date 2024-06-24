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

use Hyperf\HttpServer\Server;

class SuAdminServer extends Server
{
    protected ?string $serverName = 'SuAdmin';

    protected $routes;

    public function onRequest($request, $response): void
    {
        parent::onRequest($request, $response);
        $this->bootstrap();
    }

    /**
     * SuAdminServer bootstrap.
     */
    protected function bootstrap(): void {}
}
