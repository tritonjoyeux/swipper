<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

use Fashiongroup\Swiper\Search;

class IndeedAgentNz extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://nz.indeed.com/jobs';
    }

    public function getName()
    {
        return 'indeed_nz';
    }
}
