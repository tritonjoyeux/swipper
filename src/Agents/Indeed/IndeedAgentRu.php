<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentRu extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://ru.indeed.com/jobs';
    }

    protected function getDatePattern()
    {
        return '/(\d+) дней назад/u';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            'дней' => 'day',
            'час' => 'hour',
            'Минута' => 'minute'
        );
    }

    public function getName()
    {
        return 'indeed_ru';
    }
}
