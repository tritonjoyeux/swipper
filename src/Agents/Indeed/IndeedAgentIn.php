<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

use Fashiongroup\Swiper\Search;

class IndeedAgentIn extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://www.indeed.co.in/jobs';
    }

    public function getName()
    {
        return 'indeed_in';
    }
}
