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

namespace SuAdmin\Abstracts;

use Hyperf\Context\Context;
use SuAdmin\Traits\ServiceTrait;

abstract class AbstractService
{
    use ServiceTrait;

    public AbstractMapper $mapper;

    /**
     * 魔术方法，从类属性里获取数据.
     * @param mixed $name
     * @return mixed|string
     */
    public function __get($name)
    {
        return $this->getAttributes()[$name] ?? '';
    }

    /**
     * 把数据设置为类属性.
     */
    public function setAttributes(array $data): void
    {
        Context::set('attributes', $data);
    }

    /**
     * 获取数据.
     */
    public function getAttributes(): array
    {
        return Context::get('attributes', []);
    }
}
