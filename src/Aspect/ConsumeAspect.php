<?php
/**
 * Description:消费切面
 * Created by phpStorm.
 * User: mike
 * Date: 2021/11/19
 * Time: 下午2:14.
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

namespace SuAdmin\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use SuAdmin\Amqp\Event\AfterConsume;
use SuAdmin\Amqp\Event\BeforeConsume;
use SuAdmin\Amqp\Event\FailToConsume;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ConsumeAspect.
 */
#[Aspect]
class ConsumeAspect extends AbstractAspect
{
    public array $classes = [
        'Hyperf\Amqp\Message\ConsumerMessage::consumeMessage',
    ];

    /**
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $data = $proceedingJoinPoint->getArguments()[0];
        $message = $proceedingJoinPoint->getArguments()[1];
        try {
            su_admin_event(new BeforeConsume($message, $data));
            $result = $proceedingJoinPoint->process();
            su_admin_event(new AfterConsume($message, $data, $result));
            return $result;
        } catch (\Throwable $e) {
            su_admin_event(new FailToConsume($message, $data, $e));
            return null;
        }
    }
}
