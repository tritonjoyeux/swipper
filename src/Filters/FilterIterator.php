<?php
/**
 * Created by PhpStorm.
 * User: paul
 * Date: 28/02/17
 * Time: 15:20
 */

namespace Fashiongroup\Swiper\Filters;

use Psr\Log\LoggerInterface;
use Webmozart\KeyValueStore\Api\KeyValueStore;

class FilterIterator extends \FilterIterator
{
    private $filters = [];
    /**
     * @var KeyValueStore
     */
    private $store;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function accept()
    {
        foreach ($this->filters as $filter) {
            if (!$filter->accept($this->current(), $this->store, $this->logger)) {
                return false;
            }
        }

        return true;
    }

    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * @param KeyValueStore $store
     * @return FilterIterator
     */
    public function setStore($store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     * @param LoggerInterface $logger
     * @return FilterIterator
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
        return $this;
    }
}
