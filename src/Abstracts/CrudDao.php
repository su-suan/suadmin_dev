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

use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model;
use SuAdmin\Exception\CrudException;
use SuAdmin\Interfaces\CrudDaoContract;
use SuAdmin\Traits\CrudDaoTrait;

/**
 * CrudDao.
 * @template ModelClass
 * @implements CrudDaoContract<ModelClass>
 */
abstract class CrudDao implements CrudDaoContract
{
    use CrudDaoTrait;

    /**
     * @var class-string<ModelClass>|string
     */
    protected string $model;

    /**
     * 获取模型类名.
     * @return ModelClass
     * @throws CrudException
     */
    public function getModel(): string
    {
        $modelClass = null;
        if (property_exists($this, 'model')) {
            $modelClass = $this->model;
        }
        if (property_exists($this, 'mapper')) {
            $modelClass = $this->mapper;
        }
        if (! class_exists($modelClass) || ! is_subclass_of($modelClass, Model::class)) {
            throw new CrudException('The class to which the ' . static::class . ' class belongs was not found');
        }
        /* @var class-string<Model> $modelClass */
        return $modelClass;
    }

    /**
     * @throws CrudException
     */
    public function getModelQuery(): Builder
    {
        return $this->getModel()::query();
    }

    /**
     * @throws CrudException
     */
    public function getModelInstance(): Builder|\Hyperf\Database\Model\Model
    {
        return $this->getModelQuery()->getModel();
    }
}
