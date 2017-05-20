<?php

namespace Fashiongroup\Swiper\Workflow;

use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Search;

interface Writer
{
    public function write(JobPosting $jobPosting, Search $search);

    public function flush();
}
