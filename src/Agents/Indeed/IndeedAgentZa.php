<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentZa extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://www.indeed.co.za/jobs';
    }

    public function getName()
    {
        return 'indeed_za';
    }
}
