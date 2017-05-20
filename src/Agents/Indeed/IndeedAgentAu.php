<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

use Fashiongroup\Swiper\Search;

class IndeedAgentAu extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://au.indeed.com/jobs';
    }

    public function getName()
    {
        return 'indeed_au';
    }
}
