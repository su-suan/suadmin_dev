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

namespace SuAdmin\Crontab;

use Hyperf\Crontab\Parser;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Redis\Redis;
use SuAdmin\SuAdminModel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function Hyperf\Config\config;

/**
 * 定时任务管理器
 * Class SuAdminCrontabManage.
 */
class SuAdminCrontabManage
{
    /**
     * ContainerInterface.
     */
    #[Inject]
    protected ContainerInterface $container;

    /**
     * Parser.
     */
    #[Inject]
    protected Parser $parser;

    /**
     * ClientFactory.
     */
    #[Inject]
    protected ClientFactory $clientFactory;

    /**
     * Redis.
     */
    protected Redis $suAdminRedis;

    /**
     * SuAdminCrontabManage constructor.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct()
    {
        $this->suAdminRedis = su_admin_redis_instance();
    }

    /**
     * 获取定时任务列表.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getCrontabList(): array
    {
        $prefix = config('cache.default.prefix');
        //        $data = $this->suAdminRedis->get($prefix . 'crontab');

        //        if ($data === false) {
        $data = SettingCrontab::query()
            ->where('status', SuAdminModel::ENABLE)
            ->get(explode(',', 'id,name,type,target,rule,parameter'))->toArray();
        $this->suAdminRedis->set($prefix . 'crontab', serialize($data));
        //        } else {
        //            $data = unserialize($data);
        //        }

        if (is_null($data)) {
            return [];
        }

        $last = time();
        $list = [];

        foreach ($data as $item) {
            $crontab = new SuAdminCrontab();
            $crontab->setCallback($item['target']);
            $crontab->setType((string) $item['type']);
            $crontab->setEnable(true);
            $crontab->setCrontabId($item['id']);
            $crontab->setName($item['name']);
            $crontab->setParameter($item['parameter'] ?: '');
            $crontab->setRule($item['rule']);

            if (! $this->parser->isValid($crontab->getRule())) {
                su_admin_console()->info('Crontab task [' . $item['name'] . '] rule error, skipping execution');
                continue;
            }

            $time = $this->parser->parse($crontab->getRule(), $last);
            if ($time) {
                foreach ($time as $t) {
                    $list[] = clone $crontab->setExecuteTime($t);
                }
            }
        }
        return $list;
    }
}
