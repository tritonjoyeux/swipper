<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentPe extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://www.indeed.pe/trabajo';
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
        return 'indeed_pe';
    }
}
