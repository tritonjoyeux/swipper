<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

use Fashiongroup\Swiper\Search;

class IndeedAgentTr extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://tr.indeed.com/jobs';
    }

    protected function getDatePattern()
    {
        return '/(\d+)\+? (\w+) önce$/u';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            'gün' => 'day',
            'saat' => 'hour',
            'saater' => 'hour',
            'dakika' => 'minute'
        );
    }

    public function getName()
    {
        return 'indeed_tr';
    }
}
