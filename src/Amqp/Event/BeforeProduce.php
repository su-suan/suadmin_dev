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

class BeforeProduce
{
    public $producer;

    public $delayTime;

    public function __construct(ProducerMessageInterface $producer, int $delayTime)
    {
        $this->producer = $producer;
        $this->delayTime = $delayTime;
    }
}
