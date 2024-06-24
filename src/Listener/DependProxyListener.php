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

namespace SuAdmin\Listener;

use Hyperf\Contract\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use SuAdmin\Annotation\ComponentCollector;
use SuAdmin\Annotation\DependProxyCollector;

class DependProxyListener implements ListenerInterface
{
    public function __construct(public ContainerInterface $container) {}

    public function listen(): array
    {
        return [BootApplication::class];
    }

    public function process(object $event): void
    {
        $this->handleComponent();
        $this->handleDefined();
    }

    private function handleComponent(): void
    {
        $components = ComponentCollector::getComponent();
        $postConstructs = ComponentCollector::getPostConstruct();
        $overrides = ComponentCollector::getOverride();
        $container = $this->container;
        foreach ($components as $component) {
            if (! empty($overrides[$component])) {
                $container->define($component, ($overrides[$component])(...));
            } else {
                $componentInstance = $container->make($component);
                if (! empty($postConstructs[$component])) {
                    $constructs = $postConstructs[$component];
                    rsort($constructs, SORT_DESC);
                    foreach ($constructs as $construct) {
                        $componentInstance->{$construct}();
                    }
                }
            }
        }
    }

    private function handleDefined(): void
    {
        foreach (DependProxyCollector::list() as $collector) {
            $targets = $collector->values;
            $definition = $collector->provider;
            foreach ($targets as $target) {
                $this->container
                    ->define($target, $definition);
            }
        }
    }
}
