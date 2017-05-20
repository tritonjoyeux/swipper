<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentNo extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://no.indeed.com/jobs';
    }

    protected function getDatePattern()
    {
        return '/(\d+)\+? (\w+)/';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            'jour' => 'dag',
            'jours' => 'dager',
            'heure' => 'tid',
            'minute' => 'minutt'
        );
    }

    public function getName()
    {
        return 'indeed_no';
    }
}
