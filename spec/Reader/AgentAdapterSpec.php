<?php

namespace spec\Fashiongroup\Swiper\Reader;

use Fashiongroup\Swiper\Agents\AgentInterface;
use Fashiongroup\Swiper\Reader\AgentAdapter;
use Fashiongroup\Swiper\Search;
use PhpSpec\ObjectBehavior;
use Port\Reader;
use Prophecy\Argument;

class AgentAdapterSpec extends ObjectBehavior
{

    function let (AgentInterface $agent, Search $search) {
        $this->beConstructedWith($agent, $search);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AgentAdapter::class);
        $this->shouldImplement(Reader::class);
    }
}
