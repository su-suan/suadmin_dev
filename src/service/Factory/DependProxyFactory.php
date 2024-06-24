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

namespace SuAdmin\service\Factory;

use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function class_exists;
use function interface_exists;

class DependProxyFactory
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public static function define(string $name, string $definition, bool $isLogger = true): void
    {
        /** @var ContainerInterface $container */
        $container = ApplicationContext::getContainer();
        $config = $container->get(ConfigInterface::class);

        if (interface_exists($definition) || class_exists($definition)) {
            $config->set("dependencies.$name", $definition);
            $container->define($name, $definition);
        }
        if (interface_exists($name)) {
            $config->set("SuAdmin.dependProxy.$name", $definition);
        }

        if ($container->has($name)) {
            $isLogger && su_admin_logger()->info(
                sprintf('Dependencies [%s] Injection to the [%s] successfully.', $definition, $name)
            );
        } else {
            $isLogger && su_admin_logger()->warning(sprintf('Dependencies [%s] Injection to the [%s] failed.', $definition, $name));
        }
    }
}
