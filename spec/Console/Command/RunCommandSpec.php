<?php

namespace spec\Fashiongroup\Swiper\Console\Command;

use Fashiongroup\Swiper\Console\Command\RunCommand;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RunCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RunCommand::class);
    }
}
