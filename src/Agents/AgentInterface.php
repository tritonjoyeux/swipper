<?php

namespace Fashiongroup\Swiper\Agents;

use Fashiongroup\Swiper\Exception\ModifiedException;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Search;

interface AgentInterface extends \IteratorAggregate
{
    /**
     * @param array $cursor
     * @return JobPostingResultSet
     */
    public function search($cursor = []);

    /**
     * @param JobPosting $jobPosting
     * @return JobPosting
     */
    public function refine(JobPosting $jobPosting);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param Search $search
     * @return bool
     */
    public function support(Search $search);

    /**
     * @return Search
     */
    public function getSearch();

    /**
     * @param Search $search
     * @return AbstractAgent
     */
    public function setSearch(Search $search);

    /**
     * @param $elements
     * @throws ModifiedException
     */
    public function isModified(array $elements);

}
