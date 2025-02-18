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

use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\MetadataCollector;

class ApiCollector extends MetadataCollector
{
    protected static array $apiData = [];

    protected static array $apiDataByAppId = [];

    public static function getApiInfo(string $accessName): ?array
    {
        if (count(self::$apiData) == 0) {
            self::parse();
        }
        return self::$apiData[$accessName] ?? null;
    }

    public static function getApiInfos(): array
    {
        if (count(self::$apiData) == 0) {
            self::parse();
        }
        return self::$apiData;
    }

    public static function getApiInfosByAppId(string $id): array
    {
        if (count(self::$apiDataByAppId) == 0) {
            self::parse();
        }
        return self::$apiDataByAppId[$id] ?? [];
    }

    public static function parse(): void
    {
        if (count(self::$apiData) == 0) {
            $requestParams = ApiRequestParamCollector::result();
            $responseParams = ApiResponseParamCollector::result();
            $metadata = AnnotationCollector::getMethodsByAnnotation(Api::class);
            foreach ($metadata as $data) {
                $api_column = [];

                if (isset($requestParams[$data['class']], $requestParams[$data['class']][$data['method']])) {
                    $api_column = $requestParams[$data['class']][$data['method']];
                }

                if (isset($responseParams[$data['class']], $responseParams[$data['class']][$data['method']])) {
                    $api_column = array_merge($api_column, $responseParams[$data['class']][$data['method']]);
                }

                self::$apiData[$data['annotation']->accessName] = [
                    'id' => $data['annotation']->accessName,
                    'class_name' => $data['class'],
                    'method_name' => $data['method'],
                    'name' => $data['annotation']->name,
                    'status' => $data['annotation']->status,
                    'access_name' => $data['annotation']->accessName,
                    'request_mode' => $data['annotation']->requestMode,
                    'remark' => $data['annotation']->remark,
                    'auth_mode' => $data['annotation']->authMode,
                    'app_id' => $data['annotation']->appId,
                    'api_column' => $api_column,
                    'group_id' => $data['annotation']->groupId,
                    'response' => $data['annotation']->response,
                    'updated_at' => date('Y-m-d H:i:s', START_TIME),
                ];
                self::$apiDataByAppId[$data['annotation']->appId][] = self::$apiData[$data['annotation']->accessName];
            }
        }
    }
}
