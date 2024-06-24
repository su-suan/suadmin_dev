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

namespace SuAdmin\Utils\Snowflake;

use Hyperf\Snowflake\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    protected int $millisecondBits = 41;

    protected int $dataCenterIdBits = 2;

    protected int $workerIdBits = 4;

    protected int $sequenceBits = 5;

    public function maxWorkerId(): int
    {
        return -1 ^ (-1 << $this->workerIdBits);
    }

    public function maxDataCenterId(): int
    {
        return -1 ^ (-1 << $this->dataCenterIdBits);
    }

    public function maxSequence(): int
    {
        return -1 ^ (-1 << $this->sequenceBits);
    }

    public function getTimestampLeftShift(): int
    {
        return $this->sequenceBits + $this->workerIdBits + $this->dataCenterIdBits;
    }

    public function getDataCenterIdShift(): int
    {
        return $this->sequenceBits + $this->workerIdBits;
    }

    public function getWorkerIdShift(): int
    {
        return $this->sequenceBits;
    }

    public function getTimestampBits(): int
    {
        return $this->millisecondBits;
    }

    public function getDataCenterIdBits(): int
    {
        return $this->dataCenterIdBits;
    }

    public function getWorkerIdBits(): int
    {
        return $this->workerIdBits;
    }

    public function getSequenceBits(): int
    {
        return $this->sequenceBits;
    }
}
