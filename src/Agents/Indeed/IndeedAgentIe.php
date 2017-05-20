<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentIe extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://ie.indeed.com/jobs';
    }

    public function getName()
    {
        return 'indeed_ie';
    }
}
