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

use Composer\InstalledVersions;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Framework\Bootstrap\ServerStartCallback;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function Hyperf\Support\env;

class SuAdminStart extends ServerStartCallback
{
    private StdoutLoggerInterface $stdoutLogger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function beforeStart()
    {
        $console = su_admin_console();
        $console->info('SuAdmin start success...');
        $console->info($this->welcome());
        str_contains(PHP_OS, 'CYGWIN')
        && $console->info('current booting the user: ' . shell_exec('whoami'));
    }

    protected function welcome(): string
    {
        $projectBasePath = realpath(
            dirname(
                InstalledVersions::getInstallPath('su-suan/suadmin'),
                2
            )
        );
        if (
            env('WELCOME_FILE')
            && file_exists(
                $projectBasePath .
                DIRECTORY_SEPARATOR .
                env('WELCOME_FILE')
            )
        ) {
            $welcome = file_get_contents($projectBasePath .
                DIRECTORY_SEPARATOR .
                env('WELCOME_FILE'));
        } else {
            $welcome = '
/------------------ welcome to SuAdmin -----------------\
|      _____          ___       __          _           |
|     / ___/__  __   /   | ____/ /___ ___  (_)___       |
|     \__ \/ / / /  / /| |/ __  / __ `__ \/ / __ \      |
|    ___/ / /_/ /  / ___ / /_/ / / / / / / / / / /      |
|   /____/\__,_/  /_/  |_\__,_/_/ /_/ /_/_/_/ /_/       |
|                                                       |
\____________  Copyright SuAdmin 2024 ~ %s  ____________/
            ';
        }
        return str_replace([
            '%y',
        ], [
            date('Y'),
        ], $welcome);
    }
}
