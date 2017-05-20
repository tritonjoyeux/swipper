<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentCl extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://www.indeed.cl/trabajo';
    }

    protected function getDatePattern()
    {
        return '/hace (\d+) días/u';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            'día' => 'day',
            'hora' => 'hour',
            'minuto' => 'minute'
        );
    }

    public function getName()
    {
        return 'indeed_cl';
    }
}
