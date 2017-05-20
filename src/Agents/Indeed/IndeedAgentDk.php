<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentDk extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://dk.indeed.com/jobs';
    }

    protected function getDatePattern()
    {
        return '/(\d+)\+? (\w+)/';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            'jour' => 'dag',
            'jours' => 'dage',
            'heure' => 'tid',
            'minute' => 'minut'
        );
    }

    public function getName()
    {
        return 'indeed_dk';
    }
}
