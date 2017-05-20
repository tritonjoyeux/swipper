<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class IndeedAgentCn extends AbstractIndeedAgent
{
    protected function getListBaseUrl()
    {
        return 'https://cn.indeed.com/jobs';
    }

    protected function getDatePattern()
    {
        return '/(\d+)天前/u';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            '天' => 'day',
            '小时' => 'hour',
            '分钟' => 'minute'
        );
    }

    public function getName()
    {
        return 'indeed_cn';
    }
}
