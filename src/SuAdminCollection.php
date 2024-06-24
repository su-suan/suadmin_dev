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

namespace SuAdmin;

use Hyperf\Database\Model\Collection;
use SuAdmin\Utils\Excel\Storage\PhpOffice;
use SuAdmin\Utils\Excel\Storage\XlsWriter;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

use function Hyperf\Config\config;

class SuAdminCollection extends Collection
{
    /**
     * 系统菜单转前端路由树.
     */
    public function sysMenuToRouterTree(): array
    {
        $data = $this->toArray();
        if (empty($data)) {
            return [];
        }

        $routers = [];
        foreach ($data as $menu) {
            array_push($routers, $this->setRouter($menu));
        }
        return $this->toTree($routers);
    }

    public function setRouter(&$menu): array
    {
        $route = ($menu['type'] == 'L' || $menu['type'] == 'I') ? $menu['route'] : '/' . $menu['route'];
        return [
            'id' => $menu['id'],
            'parent_id' => $menu['parent_id'],
            'name' => $menu['code'],
            'component' => $menu['component'],
            'path' => $route,
            'redirect' => $menu['redirect'],
            'meta' => [
                'type' => $menu['type'],
                'icon' => $menu['icon'],
                'title' => $menu['name'],
                'hidden' => ($menu['is_hidden'] === 1),
                'hiddenBreadcrumb' => false,
            ],
        ];
    }

    public function toTree(array $data = [], int $parentId = 0, string $id = 'id', string $parentField = 'parent_id', string $children = 'children'): array
    {
        $data = $data ?: $this->toArray();

        if (empty($data)) {
            return [];
        }

        $tree = [];

        foreach ($data as $value) {
            if ($value[$parentField] == $parentId) {
                $child = $this->toTree($data, $value[$id], $id, $parentField, $children);
                if (! empty($child)) {
                    $value[$children] = $child;
                }
                array_push($tree, $value);
            }
        }

        unset($data);
        return $tree;
    }

    /**
     * 导出数据.
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function export(string $dto, string $filename, null|array|\Closure $closure = null, ?\Closure $callbackData = null): ResponseInterface
    {
        $excelDrive = config('SuAdmin.excel_drive');
        if ($excelDrive === 'auto') {
            $excel = extension_loaded('xlswriter') ? new XlsWriter($dto) : new PhpOffice($dto);
        } else {
            $excel = $excelDrive === 'xlsWriter' ? new XlsWriter($dto) : new PhpOffice($dto);
        }
        return $excel->export($filename, is_null($closure) ? $this->toArray() : $closure, $callbackData);
    }

    /**
     * 数据导入.
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function import(string $dto, SuAdminModel $model, ?\Closure $closure = null): bool
    {
        $excelDrive = config('SuAdmin.excel_drive');
        if ($excelDrive === 'auto') {
            $excel = extension_loaded('xlswriter') ? new XlsWriter($dto) : new PhpOffice($dto);
        } else {
            $excel = $excelDrive === 'xlsWriter' ? new XlsWriter($dto) : new PhpOffice($dto);
        }
        return $excel->import($model, $closure);
    }
}
