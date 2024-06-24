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

use Hyperf\Amqp\Message\ConsumerMessageInterface;

class AfterConsume
{
    /**
     * @var ConsumerMessageInterface
     */
    public $message;

    public $data;

    public $result;

    public function __construct($message, $data, $result)
    {
        $this->message = $message;
        $this->data = $data;
        $this->result = $result;
    }
}
