<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentCh extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://ch.indeed.com/jobs';
    }

    public function getName()
    {
        return 'indeed_ch';
    }
}
