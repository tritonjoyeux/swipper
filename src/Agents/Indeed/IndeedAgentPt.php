<?php

namespace Fashiongroup\Swiper\Agents\Indeed;


class IndeedAgentPt extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://pt.indeed.com/jobs';
    }

    protected function getDatePattern()
    {
        return '/há (\d+)\+? (\w+)/';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            'jour' => 'dia',
            'jours' => 'dias',
            'heure' => 'tempo',
            'minute' => 'minuto'
        );
    }

    public function getName()
    {
        return 'indeed_pt';
    }
}
