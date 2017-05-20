<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

use Fashiongroup\Swiper\Search;

class IndeedAgentFr extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://www.indeed.fr/emplois';
    }

    protected function getDatePattern()
    {
        return '/il y a (\d+)\+? (\w+)$/';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            'jour' => 'day',
            'heure' => 'hour',
            'minute' => 'minute'
        );
    }

    public function getName()
    {
        return 'indeed_fr';
    }
}
