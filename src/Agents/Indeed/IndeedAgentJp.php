<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentJp extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://jp.indeed.com/求人';
    }

    protected function getDatePattern()
    {
        return '/(\d+)日前/u';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            '日' => 'day',
            '小时' => 'hour',
            '分钟' => 'minute'
        );
    }

    public function getName()
    {
        return 'indeed_jp';
    }
}
