<?php

namespace Fashiongroup\Swiper\Workflow;

use Fashiongroup\Swiper\Filters\FilterInterface;
use Fashiongroup\Swiper\Filters\FilterIterator;
use Fashiongroup\Swiper\LoggerAwareInterface;
use Fashiongroup\Swiper\LoggerAwareTrait;
use Fashiongroup\Swiper\Search;
use Fashiongroup\Swiper\Swiper;
use Fashiongroup\Swiper\Workflow\Writers\WriterException;
use Psr\Log\NullLogger;

class Workflow implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var Swiper
     */
    private $swiper;

    /**
     * @var Writer[]
     */
    private $writers = [];

    /**
     * @var int
     */
    private $freshness;

    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * @var Search[]
     */
    private $searches = [];

    /**
     * Workflow constructor.
     * @param Swiper $swiper
     */
    public function __construct(Swiper $swiper)
    {
        $this->swiper = $swiper;
        $this->logger = new NullLogger();
    }

    public function run()
    {
        foreach ($this->searches as $search) {
            $this->runSingleSearch($search);
        }

        // flush writers
        foreach ($this->writers as $writer) {
            $writer->flush();
        }
    }

    private function runSingleSearch(Search $search) {

        $this->logger->info(sprintf('run search %s with %s agent', $search->getTerms(), $search->getAgent()));

        $this->swiper->setSearch($search);

        $iterator = new FilterIterator($this->swiper->getIterator());

        // add filters to filter iterator
        foreach ($this->filters as $filter) {
            $iterator->addFilter($filter);
        }

        // iterate over search results
        foreach ($iterator as $jobPosting) {
            if ($this->swiper->getSearch()->getFreshness() != null && $jobPosting->getPublishedAt() < new \DateTime('now -  ' . $this->swiper->getSearch()->getFreshness() . ' days')) {
                break;
            }

            // refine search result
            $this->swiper->refine($jobPosting);

            // write result
            foreach ($this->writers as $writer) {
                try {
                    $writer->write($jobPosting, $this->swiper->getSearch());
                } catch (WriterException $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }

    /**
     * @param FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @return int
     */
    public function getFreshness()
    {
        return $this->freshness;
    }

    /**
     * @param int $freshness
     */
    public function setFreshness($freshness)
    {
        $this->freshness = $freshness;
    }

    /**
     * @param Writer $writer
     * @return Workflow
     */
    public function addWriter(Writer $writer)
    {
        $this->writers[] = $writer;

        return $this;
    }

    /**
     * @param Search $search
     * @return Workflow
     */
    public function setSearch(Search $search)
    {
        return $this->setSearches([$search]);
    }

    /**
     * @param Search[] $searches
     * @return Workflow
     */
    public function setSearches(array $searches)
    {
        $this->searches = $searches;

        return $this;
    }
}
