<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentCo extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://co.indeed.com/trabajo';
    }

    protected function getDatePattern()
    {
        return '/hace (\d+) días/u';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            'día' => 'day',
            'tiempo' => 'hour',
            'minuto' => 'minute'
        );
    }

    public function getName()
    {
        return 'indeed_co';
    }
}
