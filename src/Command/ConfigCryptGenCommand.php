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

/**
 * Class JwtCommand.
 */
#[Command]
class ConfigCryptGenCommand extends SuAdminCommand
{
    /**
     * 生成key和向量.
     */
    protected ?string $name = 'suadmin:config-crypt-gen';

    public function configure()
    {
        parent::configure();
        $this->setHelp('run "php bin/hyperf.php suadmin:config-crypt-gen" create the key and iv for config encrypt');
        $this->setDescription('SuAdmin system gen config crypt key and iv command');
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        $key = base64_encode(random_bytes(32));
        $iv = base64_encode(random_bytes(openssl_cipher_iv_length('AES-128-CBC')));

        $this->info('config encrypt key generator successfully:' . $key);
        $this->info('config encrypt iv generator successfully:' . $iv);
    }
}
