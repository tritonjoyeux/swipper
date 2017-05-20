<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentVe extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://ve.indeed.com/jobs';
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
        return 'indeed_ve';
    }
}
