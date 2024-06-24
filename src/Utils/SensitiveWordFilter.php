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

namespace SuAdmin\Utils;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function Hyperf\Config\config;

class SensitiveWordFilter
{
    protected array $dict = [];

    /**
     * 加载词库数据，通过闭包形式，加载词库.
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \RedisException
     */
    public function loadDictData(?\Closure $closure = null): self
    {
        $key = config('cache.default.prefix') . ':sensitiveWords';
        $suAdminRedis = su_admin_redis_instance();

        if ($suAdminRedis->exists($key)) {
            $this->dict = $suAdminRedis->get($key);
        } elseif ($closure instanceof \Closure) {
            $this->dict = $closure($this);
            $suAdminRedis->rPush($key, $this->dict);
        }

        if ($this->dict) {
            foreach ($this->dict as $dict) {
                $this->addWords(trim($dict));
            }
        }

        return $this;
    }

    /**
     * 设置字典词库.
     * @return $this
     */
    public function setDict(array $dict): self
    {
        $this->dict = $dict;
        return $this;
    }

    public function getDict(): array
    {
        return $this->dict;
    }

    /**
     * 添加敏感词.
     */
    public function addWords(string $words): void
    {
        $wordArr = $this->splitStr($words);
        $curNode = &$this->dict;
        foreach ($wordArr as $char) {
            if (! isset($curNode)) {
                $curNode[$char] = [];
            }
            $curNode = &$curNode[$char];
        }
        // 标记到达当前节点完整路径为"敏感词"
        ++$curNode['end'];
    }

    /**
     * 过滤文本.
     *
     * @param string $str 原始文本
     * @param string $replace 敏感字替换字符
     * @param int $skipDistance 严格程度: 检测时允许跳过的间隔
     *
     * @return string 返回过滤后的文本
     */
    public function filter(string $str, string $replace = '*', int $skipDistance = 0): string
    {
        $maxDistance = max($skipDistance, 0) + 1;
        $strArr = $this->splitStr($str);
        $length = count($strArr);
        for ($i = 0; $i < $length; ++$i) {
            $char = $strArr[$i];

            if (! isset($this->dict[$char])) {
                continue;
            }

            $curNode = &$this->dict[$char];
            $dist = 0;
            $matchIndex = [$i];
            for ($j = $i + 1; $j < $length && $dist < $maxDistance; ++$j) {
                if (! isset($curNode[$strArr[$j]])) {
                    ++$dist;
                    continue;
                }

                $matchIndex[] = $j;
                $curNode = &$curNode[$strArr[$j]];
            }

            // 匹配
            if (isset($curNode['end'])) {
                foreach ($matchIndex as $index) {
                    $strArr[$index] = $replace;
                }
                $i = max($matchIndex);
            }
        }
        return implode('', $strArr);
    }

    /**
     * 检查是否包含敏感词.
     */
    public function checkText(array|string $strArr): bool
    {
        $strArr = is_array($strArr) ? $strArr : $this->splitStr($strArr);
        $curNode = &$this->dict;
        foreach ($strArr as $char) {
            if (! isset($curNode[$char])) {
                return false;
            }
        }
        return $curNode['end'] ?? false;
    }

    /**
     * 分割文本(注意ascii占1个字节, unicode...).
     * @return string[]
     */
    protected function splitStr(string $str): array
    {
        return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    }
}
