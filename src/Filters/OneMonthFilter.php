<?php

namespace Fashiongroup\Swiper\Filters;

use Fashiongroup\Swiper\Model\JobPosting;
use Psr\Log\LoggerInterface;
use Webmozart\KeyValueStore\Api\KeyValueStore;

class OneMonthFilter implements FilterInterface
{
    public function accept(JobPosting $jobPosting)
    {
        if ($jobPosting->getPublishedAt()) {
            return !$this->isOneMonth($jobPosting->getPublishedAt());
        }
        return false;
    }

    protected function isOneMonth($date)
    {
        return strtotime($date->format('Y-m-d H:i:s')) + (30 * 24 * 60 * 60) < time();
    }
}
