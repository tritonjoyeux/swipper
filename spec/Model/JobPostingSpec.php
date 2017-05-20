<?php

namespace spec\Fashiongroup\Swiper\Model;

use Fashiongroup\Swiper\Model\JobPosting;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JobPostingSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(JobPosting::class);
    }
}
