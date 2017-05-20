<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentBr extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://www.indeed.com.br/empregos';
    }

    protected function getDatePattern()
    {
        return '/há (\d+) horas/u';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            'dia' => 'day',
            'horas' => 'hour',
            'minuto' => 'minute'
        );
    }

    public function getName()
    {
        return 'indeed_br';
    }
}
