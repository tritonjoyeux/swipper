<?php

namespace Fashiongroup\Swiper\Agents\Indeed;


class IndeedAgentAt extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://at.indeed.com/jobs';
    }

    protected function getDatePattern()
    {
        return '/vor (\d+)\+? (\w+)/';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            'jour' => 'tag',
            'jours' => 'tage',
            'heure' => 'zeit',
            'minute' => 'minute'
        );
    }

    public function getName()
    {
        return 'indeed_at';
    }
}
