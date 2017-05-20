<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentAe extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://www.indeed.ae/jobs';
    }

    public function getName()
    {
        return 'indeed_ae';
    }
}
