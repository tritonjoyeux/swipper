<?php

namespace Fashiongroup\Swiper\Console\Command;

use Fashiongroup\Swiper\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Container;

abstract class BaseCommand extends Command
{
    /**
     * @var Swiper
     */
    protected $swiper;


    /**
     * @return Swiper
     */
    protected function getSwiper()
    {
        if (!$this->swiper) {
            $this->swiper = Factory::create();
        }

        return $this->swiper;
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        return $this->getSwiper()->getContainer();
    }
}
