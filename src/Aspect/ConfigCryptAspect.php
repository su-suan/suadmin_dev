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

namespace SuAdmin\Aspect;

use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

/**
 * Class ConfigCryptAspect.
 */
class ConfigCryptAspect extends AbstractAspect
{
    public array $classes = [
        'Hyperf\Config\ConfigFactory::__invoke',
    ];

    private ?bool $enable = null;

    private ?string $key = null;

    private ?string $iv = null;

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $result = $proceedingJoinPoint->process();
        if (is_null($this->enable)) {
            $this->enable = (bool) $result->get('SuAdmin.config_encryption', false);
        }
        if ($this->enable) {
            $this->processConfig($result);
        }
        return $result;
    }

    private function processConfig($result)
    {
        $config = (array) $result;
        $config = array_shift($config);

        foreach ($config as $key => $value) {
            if ($key != 'SuAdmin') {
                if (is_array($value)) {
                    $result->set($key, $this->processDecryption($result, $value));
                }
            }
        }
    }

    private function processDecryption($result, $config): array
    {
        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $config[$key] = $this->processDecryption($result, $value);
            } else {
                if (is_string($value)) {
                    if (preg_match('#ENC\((.*?)\)#is', $value, $matches)) {
                        if (is_null($this->key)) {
                            $this->key = $result->get('SuAdmin.config_encryption_key', '');
                            if (! empty($this->key)) {
                                $this->key = @base64_decode($this->key);
                            }
                        }

                        if (is_null($this->iv)) {
                            $this->iv = $result->get('SuAdmin.config_encryption_iv', '');
                            if (! empty($this->iv)) {
                                $this->iv = @base64_decode($this->iv);
                            }
                        }

                        $value = @openssl_decrypt($matches[1], 'AES-128-CBC', $this->key, 0, $this->iv);
                        if (empty($value)) {
                            $value = $matches[1];
                        }

                        $config[$key] = $value;
                    }
                }
            }
        }
        return $config;
    }
}
