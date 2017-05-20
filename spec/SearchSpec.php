<?php

namespace spec\Fashiongroup\Swiper;

use Fashiongroup\Swiper\Search;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SearchSpec extends ObjectBehavior
{
    function let() {
        $this->beConstructedWith('Responsable de Point de Vente');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Search::class);
    }

    function it_should_be_setted() {
        $this
            ->setSince(new \DateTime())
            ->setLocation('Paris')
            ->setCountry('FR');
    }
}
