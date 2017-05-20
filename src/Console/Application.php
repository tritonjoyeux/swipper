<?php

namespace Fashiongroup\Swiper\Console;

use Fashiongroup\Swiper\Console\Command\ClearCache;
use Fashiongroup\Swiper\Console\Command\ClearData;
use Fashiongroup\Swiper\Console\Command\ClearLogs;
use Fashiongroup\Swiper\Console\Command\RunCommand;
use Fashiongroup\Swiper\Factory;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    private static $logo = '
  _______    __ ____ ____   ___ ____  
 / ___/  |__|  |    |    \ /  _]    \ 
(   \_|  |  |  ||  ||  o  )  [_|  D  )
 \__  |  |  |  ||  ||   _/    _]    / 
 /  \ |  `  ’  ||  ||  | |   [_|    \ 
 \    |\      / |  ||  | |     |  .  \
  \___| \_/\_/ |____|__| |_____|__|\_|
    ';

    public function getHelp()
    {
        return self::$logo . parent::getHelp();
    }

    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), array(
            new RunCommand(),
            new ClearCache(),
            new ClearData(),
            new ClearLogs()
        ));
    }
}
