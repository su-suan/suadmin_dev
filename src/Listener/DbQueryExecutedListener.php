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

use Hyperf\Collection\Arr;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use SuAdmin\Utils\Str;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function Hyperf\Support\env;

#[Listener]
class DbQueryExecutedListener implements ListenerInterface
{
    protected StdoutLoggerInterface $console;

    private LoggerInterface $logger;

    public function __construct(StdoutLoggerInterface $console, ContainerInterface $container)
    {
        $this->logger = $container->get(LoggerFactory::class)->get('sql', 'sql');
        $this->console = $console;
    }

    public function listen(): array
    {
        return [QueryExecuted::class];
    }

    /**
     * @param object $event
     */
    public function process(object $event): void
    {
        if ($event instanceof QueryExecuted) {
            $sql = $event->sql;
            $offset = 0;
            if (! Arr::isAssoc($event->bindings)) {
                foreach ($event->bindings as $value) {
                    $value = is_array($value) ? json_encode($value) : "'{$value}'";
                    $sql = Str::replaceFirst('?', "{$value}", $sql, $offset);
                }
            }
            if (env('CONSOLE_SQL')) {
                $this->console->info(sprintf('SQL[%s ms] %s ', $event->time, $sql));
                $this->logger->info(sprintf('[%s] %s', $event->time, $sql));
            }
        }
    }
}
