<?php
/**
 * SuAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using SuAdmin.
 *
 * @Description 延迟队列
 * @Author mike
 * @Link   https://github.com/su-suan/suadmin
 */

declare(strict_types=1);
/**
 * This file is part of SuAdmin.
 *
 * @link     https://www.SuAdmin.com
 * @document https://doc.SuAdmin.com
 * @contact  yqhcode@qq.com
 * @license  https://github.com/su-suan/suadmin
 */

namespace SuAdmin\Amqp;

use Hyperf\Amqp\Message\ProducerMessageInterface;
use Hyperf\Amqp\Producer;
use Hyperf\Context\ApplicationContext;
use Hyperf\Di\Annotation\AnnotationCollector;
use SuAdmin\Amqp\Event\AfterProduce;
use SuAdmin\Amqp\Event\BeforeProduce;
use SuAdmin\Amqp\Event\FailToProduce;
use SuAdmin\Amqp\Event\ProduceEvent;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

use Throwable;
use function Hyperf\Support\retry;

class DelayProducer extends Producer
{
    /**
     * Db.
     * @var EventDispatcherInterface
     */
    protected EventDispatcherInterface $eventDispatcher;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    public function produce(ProducerMessageInterface $producerMessage, bool $confirm = false, int $timeout = 5, int $delayTime = 0): bool
    {
        $this->eventDispatcher = ApplicationContext::getContainer()->get(EventDispatcherInterface::class);
        return retry(1, function () use ($producerMessage, $confirm, $timeout, $delayTime) {
            return $this->produceMessage($producerMessage, $confirm, $timeout, $delayTime);
        });
    }

    /**
     * 生产消息.
     * @throws Throwable
     */
    private function produceMessage(ProducerMessageInterface $producerMessage, bool $confirm = false, int $timeout = 5, int $delayTime = 0): bool
    {
        // 连接ampq
        $connection = $this->factory->getConnection($producerMessage->getPoolName());

        $result = false;

        $this->injectMessageProperty($producerMessage);

        // 触发队列发送之前事件
        $this->eventDispatcher->dispatch(new BeforeProduce($producerMessage, $delayTime));

        // 如果过期时间为0,默认过期时间1毫秒,否则为设置的过期时间
        $expiration = $delayTime == 0 ? 500 : $delayTime * 1000;
        $message = new AMQPMessage($producerMessage->payload(), array_merge($producerMessage->getProperties(), [
            'expiration' => $expiration,
        ]));

        // 触发队列发送之中事件
        $this->eventDispatcher->dispatch(new ProduceEvent($producerMessage));

        try {
            if ($confirm) {
                $channel = $connection->getConfirmChannel();
            } else {
                $channel = $connection->getChannel();
            }

            $channel->set_ack_handler(function () use (&$result) {
                $result = true;
            });
            // 延迟配置
            $queuePrefix = strchr($producerMessage->getRoutingKey(), '.', true) . '.';
            $exchangePrefix = strchr($producerMessage->getExchange(), '.', true) . '.';
            $delayExchange = $exchangePrefix . 'delay.exchange';
            $delayQueue = $queuePrefix . 'delay.queue';
            $delayRoutingKey = $queuePrefix . 'delay.routing';
            // 定义延迟交换器
            $channel->exchange_declare($delayExchange, 'topic', false, true, false);
            // 定义延迟队列
            $channel->queue_declare($delayQueue, false, true, false, false, false, new AMQPTable([
                'x-dead-letter-exchange' => $producerMessage->getExchange(),
                'x-dead-letter-routing-key' => $producerMessage->getRoutingKey(),
            ]));
            // 绑定延迟队列到交换器上
            $channel->queue_bind($delayQueue, $delayExchange, $delayRoutingKey);
            // 发送消息到延迟交换器上
            $channel->basic_publish($message, $delayExchange, $delayRoutingKey);
            $channel->wait_for_pending_acks_returns($timeout);
        } catch (Throwable $exception) {
            // 触发队列发送失败事件
            $this->eventDispatcher->dispatch(new FailToProduce($producerMessage, $exception));

            isset($channel) && $channel->close();
            throw $exception;
        }

        if ($confirm) {
            $connection->releaseChannel($channel, true);
        } else {
            $result = true;
            $connection->releaseChannel($channel);
        }
        // 触发队列发送之后事件
        $this->eventDispatcher->dispatch(new AfterProduce($producerMessage));

        return $result;
    }

    private function injectMessageProperty(ProducerMessageInterface $producerMessage)
    {
        if (class_exists(AnnotationCollector::class)) {
            /** @var null|\Hyperf\Amqp\Annotation\Producer $annotation */
            $annotation = AnnotationCollector::getClassAnnotation(get_class($producerMessage), \Hyperf\Amqp\Annotation\Producer::class);
            if ($annotation) {
                $annotation->routingKey && $producerMessage->setRoutingKey($annotation->routingKey);
                $annotation->exchange && $producerMessage->setExchange($annotation->exchange);
            }
        }
    }
}
