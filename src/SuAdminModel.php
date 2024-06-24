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

use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;
use SuAdmin\Traits\ModelMacroTrait;

/**
 * Class SuAdminModel.
 */
class SuAdminModel extends Model
{
    use Cacheable;
    use ModelMacroTrait;

    /**
     * 状态
     */
    public const ENABLE = 1;

    public const DISABLE = 0;

    /**
     * 默认每页记录数.
     */
    public const PAGE_SIZE = 10;

    /**
     * 隐藏的字段列表.
     * @var string[]
     */
    protected array $hidden = ['deleted_at'];

    /**
     * 数据权限字段，表中需要有此字段.
     */
    protected string $dataScopeField = 'created_by';

    /**
     * SuAdminModel constructor.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // 注册常用方法
        $this->registerBase();
    }

    /**
     * 设置主键的值
     * @param int|string $value
     */
    public function setPrimaryKeyValue(int|string $value): void
    {
        $this->{$this->primaryKey} = $value;
    }

    public function getPrimaryKeyType(): string
    {
        return $this->keyType;
    }

    public function newCollection(array $models = []): SuAdminCollection
    {
        return new SuAdminCollection($models);
    }

    public function getDataScopeField(): string
    {
        return $this->dataScopeField;
    }

    public function setDataScopeField(string $name): self
    {
        $this->dataScopeField = $name;
        return $this;
    }
}
