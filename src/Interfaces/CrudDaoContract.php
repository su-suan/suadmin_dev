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

namespace SuAdmin\Interfaces;

use Hyperf\Collection\Collection;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use SuAdmin\Exception\CrudException;

/**
 * CrudDao 锲约.
 * @template ModelClass for model
 * @template Param for dto
 */
interface CrudDaoContract
{
    /**
     * 分页列表查询
     * @param array|Param $params 查询条件
     * @param int $page 页码
     * @param int $size 页数
     */
    public function page(mixed $params = null, int $page = 1, int $size = 10): LengthAwarePaginatorInterface;

    /**
     * 查询总记录数.
     * @param array|Param $params 查询条件
     */
    public function count(mixed $params = null): int;

    /**
     * 查询所有列表
     * @return Collection<string,ModelClass>
     */
    public function list(mixed $params = null): Collection;

    /**
     * 根据主键查询一条记录
     * @return Collection<string,ModelClass>
     */
    public function getById(mixed $id): Collection;

    /** 句柄搜索
     * @param mixed|Param $params
     */
    public function handleSearch(Builder $query, mixed $params = null): Builder;

    /** 新增或者修改
     * @param array $data
     * @param array|null $where
     * @return Model
     */
    public function saveOrUpdate(array $data, ?array $where = null): Model;

    /**
     * 批量更新或新增
     * @param null|array $whereKeys 对应的key值
     * @param int $batchSize 分批处理数量
     * @return Collection<string,ModelClass>
     * @throws CrudException
     */
    public function batchSaveOrUpdate(
        array $data,
        ?array $whereKeys = null,
        int $batchSize = 0
    ): Collection;

    /**
     * 关联保存插入数据,
     * 数组是否具有关联性。如果数组没有以零开头的连续数字键，那么它就是“关联的”
     * @return ModelClass
     * @throws CrudException
     */
    public function save(array $data, ?array $withs = null): Model;

    /**
     * 批量插入
     * @return Collection<int,ModelClass>
     * @throws CrudException
     */
    public function batchSave(array $data): Collection;

    /**
     * 单条插入插入新数据,不支持关联插入
     * @throws CrudException
     */
    public function insert(array $data): bool;

    /**
     * 批量插入
     * 批量插入失败，则遍历保存
     * @throws CrudException
     */
    public function batchInsert(array $data): bool;

    /**
     * 根据条件删除单条记录 触发 model 事件.
     * @param array|Closure|int|string $idOrWhere 主键或自定义条件
     * @param bool $force 如果模型有软删除的话是否强制删除
     */
    public function remove(mixed $idOrWhere, bool $force = false): bool;

    /**
     * 根据主键删除单条记录 不会触发 model 事件.
     * @param array|Closure|int|string $id 主键或自定义条件
     */
    public function delete(mixed $id): bool;

    /**
     * 根据主键批量删除.
     */
    public function removeByIds(array $ids): bool;
}
