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

namespace SuAdmin\Command;

use Hyperf\Command\Annotation\Command;
use SuAdmin\SuAdminCommand;
use Symfony\Component\Console\Input\InputArgument;

use function Hyperf\Config\config;

/**
 * Class JwtCommand.
 */
#[Command]
class ConfigCryptCommand extends SuAdminCommand
{
    /**
     * 生成JWT密钥命令.
     */
    protected ?string $name = 'suadmin:config-crypt';

    public function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php suadmin:config-crypt" encrypt');
        $this->setDescription('SuAdmin system config crypt command');
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        $value = $this->input->getArgument('value');
        $key = config('SuAdmin.config_encryption_key', '');
        if (empty($key)) {
            $this->line('Not found SuAdmin.config_encryption_key config.', 'error');
            return self::FAILURE;
        }

        $key = @base64_decode($key);
        if (empty($key)) {
            $this->line('key content error.', 'error');
            return self::FAILURE;
        }

        $iv = config('SuAdmin.config_encryption_iv', '');
        if (empty($iv)) {
            $this->line('Not found SuAdmin.config_encryption_iv config.', 'error');
            return self::FAILURE;
        }

        $iv = @base64_decode($iv);
        if (empty($iv)) {
            $this->line('iv content error.', 'error');
            return self::FAILURE;
        }

        $encrypt = @openssl_encrypt($value, 'AES-128-CBC', $key, 0, $iv);

        if (empty($encrypt)) {
            $this->line('iv or key content error.please regen', 'error');
            return self::FAILURE;
        }

        $this->info('config crypt string is: ENC(' . $encrypt . ')');
    }

    protected function getArguments()
    {
        return [
            ['value', InputArgument::REQUIRED, 'source value'],
        ];
    }
}
