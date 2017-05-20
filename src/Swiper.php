<?php

namespace Fashiongroup\Swiper;

use Fashiongroup\Swiper\Agents\CompositeAgent;
use \Symfony\Component\DependencyInjection\Container;

class Swiper extends CompositeAgent
{
    /**
     * @var Container
     */
    private $container;

    public function getName()
    {
        return 'swiper';
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     * @return Swiper
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
        return $this;
    }
}
