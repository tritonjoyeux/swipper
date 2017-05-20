<?php

namespace spec\Fashiongroup\Swiper\Console;

use Fashiongroup\Swiper\Console\Application;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApplicationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Application::class);
    }
}
