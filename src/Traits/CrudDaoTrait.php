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

namespace SuAdmin\Traits;

use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\DbConnection\Db;
use SuAdmin\Abstracts\CrudDao;
use SuAdmin\Exception\CrudException;
use SuAdmin\Interfaces\CrudDaoContract;

/**
 * @mixin CrudDao
 * @mixin CrudDaoContract
 */
trait CrudDaoTrait
{
    /** 分页列表查询
     * @param mixed|null $params 查询条件
     * @param int $page 页码
     * @param int $size 页数
     * @return LengthAwarePaginatorInterface
     * @throws CrudException
     */
    public function page(mixed $params = null, int $page = 1, int $size = 10): LengthAwarePaginatorInterface
    {
        return $this->handleSearch(
            $this->handleSelect($this->preQuery()),
            $params
        )->paginate(perPage: $size, page: $page);
    }

    /** 查询所有列表
     * @param mixed|null $params 查询条件
     * @return int
     * @throws CrudException
     */
    public function count(mixed $params = null): int
    {
        return $this->handleSearch(
            $this->preQuery(),
            $params
        )->count();
    }

    /** 列表查询
     * @param mixed|null $params
     * @return Collection
     * @throws CrudException
     */
    public function list(mixed $params = null): Collection
    {
        return $this->handleSearch(
            $this->handleSelect($this->preQuery()),
            $params
        )->get();
    }

    /** 根据主键查询一条记录
     * @param mixed $id
     * @return Collection
     * @throws CrudException
     */
    public function getById(mixed $id): Collection
    {
        return Collection::make($this->getModel()::find($id));
    }

    /** 句柄搜索
     * @param Builder $query
     * @param mixed|null $params
     * @return Builder
     */
    abstract public function handleSearch(Builder $query, mixed $params = null): Builder;

    protected function handleSelect(Builder $query): Builder
    {
        return $query->select($this->getSelectFields() ?? ['*']);
    }

    /**
     * 查询列.
     * @throws CrudException
     */
    protected function getSelectFields(): array
    {
        return $this->getModelInstance()->getFillable();
    }

    /** 预查询
     * initialization DbBuilder.
     * @throws CrudException
     */
    protected function preQuery(): Builder
    {
        return $this
            ->getModelQuery();
    }

    /** 新增或者修改
     * @param array $data
     * @param array|null $where
     * @return Model
     * @throws CrudException
     */
    public function saveOrUpdate(array $data, ?array $where = null): Model
    {
        $keyName = $this->getModelInstance()->getKeyName();
        if ($where === null) {
            return $this->getModel()::updateOrCreate(
                Arr::only($data, [$keyName]),
                $data
            );
        }
        return $this->getModelQuery()->updateOrCreate($where, $data);
    }

    /** 批量更新或新增
     * @param array $data
     * @param array|null $whereKeys
     * @param int $batchSize
     * @return Collection
     */
    public function batchSaveOrUpdate(
        array  $data,
        ?array $whereKeys = null,
        int    $batchSize = 0
    ): Collection
    {
        return Db::transaction(function () use ($data, $whereKeys) {
            $result = [];
            foreach ($data as $item) {
                if ($whereKeys === null) {
                    $result[] = $this->saveOrUpdate(
                        $item
                    );
                } else {
                    $result[] = $this->saveOrUpdate(
                        $item,
                        Arr::only($item, $whereKeys)
                    );
                }
            }
            return Collection::make($result);
        });
    }

    /** 关联保存插入数据
     * @param array $data
     * @param array|null $withs
     * @return Model
     */
    public function save(array $data, ?array $withs = null): Model
    {
        return Db::transaction(function () use ($data, $withs) {
            $modelClass = $this->getModel();
            $withAttr = [];
            if ($withs !== null) {
                foreach ($withs as $with) {
                    if (!empty($data[$with])) {
                        $withAttr[$with] = $data[$with];
                        unset($data[$with]);
                    }
                }
            }
            $model = $modelClass::create($data);
            if (!empty($withAttr)) {
                foreach ($withAttr as $with => $attr) {
                    if (method_exists($model, $with)) {
                        /**
                         * @var HasMany|HasOne $withFunc
                         */
                        $withFunc = $model->{$with}();
                        // 数组是否具有关联性。如果数组没有以零开头的连续数字键，那么它就是“关联的”
                        if (Arr::isAssoc($attr)) {
                            $withFunc->saveMany($attr);
                        } else {
                            $withFunc->save($attr);
                        }
                    }
                }
            }
            return $model;
        });
    }

    /** 批量插入
     * @param array $data
     * @return Collection
     */
    public function batchSave(array $data): Collection
    {
        return Db::transaction(function () use ($data) {
            $result = [];
            foreach ($data as $attr) {
                $with = $attr['__with__'] ?? null;
                unset($attr['__with__']);
                $result = $this->save($attr, $with);
            }
            return $result;
        });
    }

    /** 单条插入插入新数据,不支持关联插入
     * @param array $data
     * @return bool
     * @throws CrudException
     */
    public function insert(array $data): bool
    {
        return $this->getModel()::insert($data);
    }

    /** 批量插入新数据
     * @param array $data
     * @return bool
     */
    public function batchInsert(array $data): bool
    {
        Db::transaction(function () use ($data) {
            if (!$this->insert($data)) {
                foreach ($data as $attr) {
                    $this->getModel()::save($attr);
                }
            }
        });
        return true;
    }

    /** 根据条件删除单条记录 触发 model 事件.
     * @param mixed $idOrWhere 主键或自定义条件
     * @param bool $force 如果模型有软删除的话是否强制删除
     * @return bool
     */
    public function remove(mixed $idOrWhere, bool $force = false): bool
    {
        return Db::transaction(function () use ($idOrWhere, $force) {
            $modelClass = $this->getModel();
            $query = $modelClass::query();
            /**
             * @var null|bool|\Hyperf\DbConnection\Model\Model $instance
             */
            $instance = false;
            if (is_array($idOrWhere)) {
                $instance = $query->where($idOrWhere)->first();
            }
            if (is_callable($idOrWhere)) {
                $instance = $query->where($idOrWhere)->first();
            }
            if ($instance === false) {
                $instance = $query->find($idOrWhere);
            }
            if (empty($instance)) {
                return false;
            }
            if ($force) {
                return $instance->forceDelete();
            }
            return false;
        });
    }

    /** 根据主键删除数据
     * @param mixed $id
     * @return bool
     * @throws CrudException
     */
    public function delete(mixed $id): bool
    {
        $model = $this->getModel();
        $query = $model::query()->getModel();
        $keyName = $query->getModel()->getKeyName();
        /**
         * @var null|Model $instance
         */
        $instance = false;
        if (is_array($id)) {
            $instance = $query->where($id)->first();
        }
        if (is_callable($id)) {
            $instance = $query->where($id)->first();
        }
        if ($instance === null) {
            $instance = $query->find($id);
        }
        if (empty($instance)) {
            return false;
        }
        return $model::query()
            ->where(
                $keyName,
                $instance->getKey()
            )->delete();
    }

    /** 根据主键批量删除
     * @param array $ids
     * @return bool
     * @throws CrudException
     */
    public function removeByIds(array $ids): bool
    {
        $query = $this->getModelQuery();
        $keyName = $query->getModel()->getKeyName();
        return $query->whereIn($keyName, $ids)->delete();
    }
}
