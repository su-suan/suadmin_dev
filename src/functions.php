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

use Hyperf\Context\ApplicationContext;
use Hyperf\Context\Context;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Redis\Redis;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use SuAdmin\SuAdminCollection;
use SuAdmin\Utils\AppVerify;
use SuAdmin\Utils\Snowflake\SnowflakeIdGenerator;
use function Hyperf\Config\config;
use function Hyperf\Translation\__;


if (! function_exists('su_admin_container')) {
    /**
     * 获取容器实例.
     */
    function su_admin_container(): ContainerInterface
    {
        return ApplicationContext::getContainer();
    }
}

if (! function_exists('su_admin_redis_instance')) {
    /**
     * 获取Redis实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function su_admin_redis_instance(): Redis
    {
        return su_admin_container()->get(Redis::class);
    }
}

if (! function_exists('su_admin_console')) {
    /**
     * 获取控制台输出实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function su_admin_console(): StdoutLoggerInterface
    {
        return su_admin_container()->get(StdoutLoggerInterface::class);
    }
}

if (! function_exists('su_admin_logger')) {
    /**
     * 获取日志实例.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function su_admin_logger(string $name = 'Log'): LoggerInterface
    {
        return su_admin_container()->get(LoggerFactory::class)->get($name);
    }
}

if (! function_exists('su_admin_format_size')) {
    /**
     * 格式化大小.
     */
    function su_admin_format_size(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $index = 0;
        for ($i = 0; $size >= 1024 && $i < 5; ++$i) {
            $size /= 1024;
            $index = $i;
        }
        return round($size, 2) . $units[$index];
    }
}

if (! function_exists('su_admin_lang')) {
    /**
     * 获取当前语言
     */
    function su_admin_lang(): string
    {
        $acceptLanguage = su_admin_container()
            ->get(RequestInterface::class)
            ->getHeaderLine('accept-language');
        return str_replace('-', '_', ! empty($acceptLanguage) ? explode(',', $acceptLanguage)[0] : 'zh_CN');
    }
}

if (! function_exists('su_admin_multilingual')) {
    /**
     * 多语言函数.
     */
    function su_admin_multilingual(string $key, array $replace = []): string
    {
        return __($key, $replace, su_admin_lang());
    }
}

if (! function_exists('su_admin_collect')) {
    /**
     * 创建一个SuAdmin的集合类.
     * @param mixed|null $value
     */
    function su_admin_collect(mixed $value = null): SuAdminCollection
    {
        return new SuAdminCollection($value);
    }
}

if (! function_exists('su_admin_set_context')) {
    /**
     * 设置上下文数据.
     * @param string $key
     * @param mixed $data
     * @return bool
     */
    function su_admin_set_context(string $key, mixed $data): bool
    {
        return (bool) Context::set($key, $data);
    }
}

if (! function_exists('su_admin_get_context')) {
    /**
     * 获取上下文数据.
     * @param string $key
     * @return mixed
     */
    function su_admin_get_context(string $key): mixed
    {
        return Context::get($key);
    }
}

if (! function_exists('su_admin_app_verify')) {
    /**
     * 获取APP应用请求实例.
     */
    function su_admin_app_verify(string $scene = 'api'): AppVerify
    {
        return new AppVerify($scene);
    }
}

if (! function_exists('su_admin_snowflake_id')) {
    /**
     * 生成雪花ID.
     */
    function su_admin_snowflake_id(): string
    {
        return (string) su_admin_container()->get(SnowflakeIdGenerator::class)->generate();
    }
}

if (! function_exists('su_admin_uuid')) {
    /**
     * 生成UUID.
     */
    function su_admin_uuid(): string
    {
        return Uuid::uuid4()->toString();
    }
}

if (! function_exists('su_admin_event')) {
    /**
     * 事件调度快捷方法.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function su_admin_event(object $dispatch): object
    {
        return su_admin_container()->get(EventDispatcherInterface::class)->dispatch($dispatch);
    }
}

if (! function_exists('su_admin_is_empty')) {
    /**
     * 判断给定的值是否为空.
     */
    function su_admin_is_empty(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }
}

if (! function_exists('su_admin_filled')) {
    /**
     * 判断给定的值是否不为空.
     */
    function su_admin_filled(mixed $value): bool
    {
        return ! su_admin_is_empty($value);
    }
}

if (! function_exists('su_admin_http_header')) {
    /**
     * 设置header.
     */
    function su_admin_http_header(ResponseInterface $response, string $config = 'su_admin_http.header'): ResponseInterface
    {
        $config = config($config);
        foreach ($config as $key => $value) {
            $response = $response->withHeader($key, $value);
        }
        return $response;
    }

    if (!function_exists('su_admin_translator')) {
        /**
         * 多语言函数.
         */
        function su_admin_translator(string $key, array $replace = []): string
        {
            return __($key, $replace, su_admin_lang());
        }
    }

}
