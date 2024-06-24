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

namespace SuAdmin\Amqp\Event;

use Hyperf\Amqp\Message\ProducerMessageInterface;

class FailToProduce extends ConsumeEvent
{
    /**
     * @var \Throwable
     */
    public $throwable;

    public function __construct(ProducerMessageInterface $producer, \Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }
}
