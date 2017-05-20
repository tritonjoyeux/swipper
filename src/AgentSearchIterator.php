<?php

namespace Fashiongroup\Swiper;

use Fashiongroup\Swiper\Agents\AgentInterface;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Model\JobPosting;

class AgentSearchIterator implements \Iterator
{
    /**
     * @var AgentInterface
     */
    private $agent;

    private $nextCursor;

    private $nbFetchedElements;

    /**
     * @var JobPostingResultSet
     */
    private $currentResultSet;

    public function __construct(AgentInterface $agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return bool
     */
    protected function hasNextResultSet()
    {
        return $this->nextCursor !== false;
    }

    /**
     * load next result set
     */
    protected function loadNextResultSet()
    {
        $resultSet = $this->agent->search($this->nextCursor);

        $this->nextCursor = $resultSet->getNextResultSetCursor();
        $this->nbFetchedElements += $resultSet->count();

        $this->currentResultSet = $resultSet;
    }

    public function current()
    {
        /** @var JobPosting $current */
        $current = $this->currentResultSet->getIterator()->current();

        return $current;
    }

    public function next()
    {
        return $this->currentResultSet->getIterator()->next();
    }

    public function key()
    {
        return $this->currentResultSet->getIterator()->key();
    }

    public function valid()
    {
        $valid = $this->currentResultSet->getIterator()->current() instanceof JobPosting;

        if ($valid) {
            return true;
        }

        if (!$this->hasNextResultSet()) {
            return false;
        }

        $this->loadNextResultSet();

        return $this->valid();
    }

    public function rewind()
    {
        // init
        $this->nextCursor = [];
        $this->nbFetchedElements = 0;
        $this->currentResultSet = null;

        $this->loadNextResultSet();
    }

    /**
     * @return AgentInterface
     */
    public function getAgent()
    {
        return $this->agent;
    }
}
