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

use Hyperf\DbConnection\Db;
use SuAdmin\Abstracts\AbstractMapper;
use SuAdmin\SuAdminCollection;
use SuAdmin\SuAdminModel;
use SuAdmin\SuAdminResponse;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

use function Hyperf\Collection\collect;

trait ServiceTrait
{
    /**
     * @var AbstractMapper
     */
    public AbstractMapper $mapper;

    /** 获取列表数据
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getList(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = false;
        return $this->mapper->getList($params, $isScope);
    }

    /** 从回收站过去列表数据
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getListByRecycle(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = true;
        return $this->mapper->getList($params, $isScope);
    }

    /** 获取分页数据列表
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getPageList(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        return $this->mapper->getPageList($params, $isScope);
    }

    /** 从回收站获取分页数据列表
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getPageListByRecycle(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = true;
        return $this->mapper->getPageList($params, $isScope);
    }

    /** 获取树列表
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getTreeList(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = false;
        return $this->mapper->getTreeList($params, $isScope);
    }

    /** 从回收站获取树列表
     * @param array|null $params
     * @param bool $isScope
     * @return array
     */
    public function getTreeListByRecycle(?array $params = null, bool $isScope = true): array
    {
        if ($params['select'] ?? null) {
            $params['select'] = explode(',', $params['select']);
        }
        $params['recycle'] = true;
        return $this->mapper->getTreeList($params, $isScope);
    }

    /** 新增数据
     * @param array $data
     * @return mixed
     */
    public function save(array $data): mixed
    {
        return $this->mapper->save($data);
    }

    /** 批量新增
     * @param array $collects
     * @return bool
     */
    public function batchSave(array $collects): bool
    {
        return Db::transaction(function () use ($collects) {
            foreach ($collects as $collect) {
                $this->mapper->save($collect);
            }
            return true;
        });
    }

    /** 读取一条数据
     * @param mixed $id
     * @param array $column
     * @return SuAdminModel|null
     */
    public function read(mixed $id, array $column = ['*']): ?SuAdminModel
    {
        return $this->mapper->read($id, $column);
    }

    /** 获取单个值
     * @param array $condition
     * @param string $columns
     * @return mixed
     */
    public function value(array $condition, string $columns = 'id'): mixed
    {
        return $this->mapper->value($condition, $columns);
    }

    /** 获取单列值
     * @param array $condition
     * @param string $columns
     * @return array
     */
    public function pluck(array $condition, string $columns = 'id'): array
    {
        return $this->mapper->pluck($condition, $columns);
    }

    /** 从回收站读取一条数据
     * @param mixed $id
     * @return SuAdminModel
     */
    public function readByRecycle(mixed $id): SuAdminModel
    {
        return $this->mapper->readByRecycle($id);
    }

    /** 单个或批量软删除数据
     * @param array $ids
     * @return bool
     */
    public function delete(array $ids): bool
    {
        return ! empty($ids) && $this->mapper->delete($ids);
    }

    /** 更新一条数据
     * @param mixed $id
     * @param array $data
     * @return bool
     */
    public function update(mixed $id, array $data): bool
    {
        return $this->mapper->update($id, $data);
    }

    /** 按条件更新数据
     * @param array $condition
     * @param array $data
     * @return bool
     */
    public function updateByCondition(array $condition, array $data): bool
    {
        return $this->mapper->updateByCondition($condition, $data);
    }

    /** 单个或批量真实删除数据
     * @param array $ids
     * @return bool
     */
    public function realDelete(array $ids): bool
    {
        return ! empty($ids) && $this->mapper->realDelete($ids);
    }

    /** 单个或批量从回收站恢复数据
     * @param array $ids
     * @return bool
     */
    public function recovery(array $ids): bool
    {
        return ! empty($ids) && $this->mapper->recovery($ids);
    }

    /** 单个或批量禁用数据
     * @param array $ids
     * @param string $field
     * @return bool
     */
    public function disable(array $ids, string $field = 'status'): bool
    {
        return ! empty($ids) && $this->mapper->disable($ids, $field);
    }

    /** 单个或批量启用数据
     * @param array $ids
     * @param string $field
     * @return bool
     */
    public function enable(array $ids, string $field = 'status'): bool
    {
        return ! empty($ids) && $this->mapper->enable($ids, $field);
    }

    /** 修改数据状态
     * @param mixed $id
     * @param string $value
     * @param string $filed
     * @return bool
     */
    public function changeStatus(mixed $id, string $value, string $filed = 'status'): bool
    {
        return $value == SuAdminModel::ENABLE ? $this->mapper->enable([$id], $filed) : $this->mapper->disable([$id], $filed);
    }

    /** 数字更新操作
     * @param mixed $id
     * @param string $field
     * @param int $value
     * @return bool
     */
    public function numberOperation(mixed $id, string $field, int $value): bool
    {
        return $this->mapper->numberOperation($id, $field, $value);
    }

    /** 导出数据
     * @param array $params
     * @param string|null $dto
     * @param string|null $filename
     * @param \Closure|null $callbackData
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function export(array $params, ?string $dto, ?string $filename = null, ?\Closure $callbackData = null): ResponseInterface
    {
        if (empty($dto)) {
            return su_admin_container()->get(SuAdminResponse::class)->error('导出未指定DTO');
        }

        if (empty($filename)) {
            $filename = $this->mapper->getModel()->getTable();
        }

        return (new SuAdminCollection())->export($dto, $filename, $this->mapper->getList($params), $callbackData);
    }

    /** 数据导入
     * @param string $dto
     * @param \Closure|null $closure
     * @return bool
     */
    public function import(string $dto, ?\Closure $closure = null): bool
    {
        return Db::transaction(function () use ($dto, $closure) {
            return $this->mapper->import($dto, $closure);
        });
    }

    /** 数组数据转分页数据显示
     * @param array|null $params
     * @param string $pageName
     * @return array
     */
    public function getArrayToPageList(?array $params = [], string $pageName = 'page'): array
    {
        $collect = $this->handleArraySearch(collect($this->getArrayData($params)), $params);

        $pageSize = SuAdminModel::PAGE_SIZE;
        $page = 1;

        if ($params[$pageName] ?? false) {
            $page = (int) $params[$pageName];
        }

        if ($params['pageSize'] ?? false) {
            $pageSize = (int) $params['pageSize'];
        }

        $data = $collect->forPage($page, $pageSize)->toArray();

        return [
            'items' => $this->getCurrentArrayPageBefore($data, $params),
            'pageInfo' => [
                'total' => $collect->count(),
                'currentPage' => $page,
                'totalPage' => ceil($collect->count() / $pageSize),
            ],
        ];
    }

    /** 远程通用列表查询
     * @param array $params
     * @return array
     */
    public function getRemoteList(array $params = []): array
    {
        $remoteOption = $params['remoteOption'] ?? [];
        unset($params['remoteOption']);
        return $this->mapper->getRemoteList(array_merge($params, $remoteOption));
    }

    /** 数组数据搜索器
     * @param \Hyperf\Collection\Collection $collect
     * @param array $params
     * @return \Hyperf\Collection\Collection
     */
    protected function handleArraySearch(\Hyperf\Collection\Collection $collect, array $params): \Hyperf\Collection\Collection
    {
        return $collect;
    }

    /** 数组当前页数据返回之前处理器，默认对key重置.
     * @param array $data
     * @param array $params
     * @return array
     */
    protected function getCurrentArrayPageBefore(array &$data, array $params = []): array
    {
        sort($data);
        return $data;
    }

    /** 设置需要分页的数组数据
     * @param array $params
     * @return array
     */
    protected function getArrayData(array $params = []): array
    {
        return [];
    }
}
