<?php

namespace Fashiongroup\Swiper\Agents\Indeed;


class IndeedAgentNl extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://nl.indeed.com/jobs';
    }

    protected function getDatePattern()
    {
        return '/(\d+)\+? dagen geleden/';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            'jour' => 'dag',
            'jours' => 'dagen',
            'heure' => 'tijd',
            'minute' => 'minuut'
        );
    }

    public function getName()
    {
        return 'indeed_nl';
    }
}
