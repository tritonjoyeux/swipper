<?php

namespace Fashiongroup\Swiper\Filters;

use Doctrine\Common\Collections\Collection;
use Fashiongroup\Swiper\Model\JobPosting;
use Psr\Log\LoggerInterface;
use Webmozart\KeyValueStore\Api\KeyValueStore;

interface FilterInterface
{
    /**
     * @param JobPosting $jobPosting
     * @return bool
     */
    public function accept(JobPosting $jobPosting);
}
