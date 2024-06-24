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

namespace SuAdmin\Annotation\Api;

use Hyperf\Di\MetadataCollector;
use SuAdmin\Interfaces\ServiceInterface\DictDataServiceInterface;

class ApiResponseParamCollector extends MetadataCollector
{
    protected static array $container = [];

    protected static array $result = [];

    public static function collectMethod(string $class, string $method, string $annotation, $value): void
    {
        static::$container[$class][$method][] = $value;
    }

    public static function result(): array
    {
        if (count(static::$result) == 0) {
            static::parseParams();
        }
        return static::$result;
    }

    public static function parseParams()
    {
        $service = su_admin_container()->get(DictDataServiceInterface::class);
        $dataType = $service->getList([
            'code' => 'api_data_type',
        ]);
        $dataTypeMap = \Hyperf\Collection\collect($dataType)->pluck('key', 'title')->toArray();
        foreach (static::list() as $class => $methods) {
            $result = [];
            foreach ($methods as $method => $requestParams) {
                foreach ($requestParams as $requestParam) {
                    $result[$method][] = [
                        'name' => $requestParam->name,
                        'description' => $requestParam->description,
                        'data_type' => $dataTypeMap[$requestParam->dataType] ?? 'String',
                        'is_required' => $requestParam->isRequired,
                        'status' => $requestParam->status,
                        'default_value' => $requestParam->defaultValue,
                        'type' => 2,
                        'updated_at' => date('Y-m-d H:i:s', START_TIME),
                    ];
                }
            }
            static::$result[$class] = $result;
        }
    }
}
