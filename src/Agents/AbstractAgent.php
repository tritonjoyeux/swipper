<?php

namespace Fashiongroup\Swiper\Agents;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Fashiongroup\Swiper\AgentSearchIterator;
use Fashiongroup\Swiper\Exception\ModifiedException;
use Fashiongroup\Swiper\Filters\FilterInterface;
use Fashiongroup\Swiper\Filters\FilterIterator;
use Fashiongroup\Swiper\LoggerAwareTrait;

use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Search;
use Fashiongroup\Swiper\StoreAwareTrait;

abstract class AbstractAgent implements AgentInterface
{
    protected $search;

    protected $iterator = null;

    /**
     * @return JobPosting
     */
    public function createJobPosting()
    {
        $jobPosting = new JobPosting();

        return $jobPosting->setSource($this->getName());
    }

    public function support(Search $search)
    {
        return $search->getAgent() == $this->getName();
    }

    /**
     * @return Search
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param Search $search
     * @return AbstractAgent
     */
    public function setSearch(Search $search)
    {
        $this->search = $search;
        $this->iterator = null;

        return $this;
    }

    public function getIterator()
    {
        if (!$this->iterator) {
            $this->iterator = $this->createIterator();
        }

        return $this->iterator;
    }

    private function createIterator()
    {
        return new AgentSearchIterator($this);
    }

    public function isModified(array $elements)
    {
        foreach ($elements as $element){
            if($element == null || $element == false){
                throw new ModifiedException("Le parseur n'est pas à jour");
            }
        }
    }
}
