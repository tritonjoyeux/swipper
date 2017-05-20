<?php

namespace Fashiongroup\Swiper\Agents;

use Doctrine\Common\Collections\Collection;

class JobPostingResultSet implements \IteratorAggregate, \Countable
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var array
     */
    private $nextResultSetCursor;

    /**
     * JobPostingResultSet constructor.
     * @param Collection $collection
     * @param mixed $nextResultSetCursor
     */
    public function __construct(Collection $collection, $nextResultSetCursor = null)
    {
        $this->collection = $collection;
        $this->nextResultSetCursor = $nextResultSetCursor;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return array
     */
    public function getNextResultSetCursor()
    {
        return $this->nextResultSetCursor;
    }

    public function getIterator()
    {
        return $this->collection;
    }

    public function count()
    {
        return $this->collection->count();
    }
}
