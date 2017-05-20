<?php

namespace spec\Fashiongroup\Swiper;

use Fashiongroup\Swiper\Swiper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SwiperSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Swiper::class);
    }
}
