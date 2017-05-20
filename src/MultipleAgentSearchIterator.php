<?php

namespace Fashiongroup\Swiper;

use Fashiongroup\Swiper\Agents\AgentInterface;
use Fashiongroup\Swiper\Model\JobPosting;

class MultipleAgentSearchIterator implements \Iterator
{
    private $agents = [];

    private $index = -1;

    private $current;

    public function current()
    {
        return $this->current;
    }

    public function next()
    {
        $this->current = $this->getNextJobPosting();
        $this->index++;
    }

    public function key()
    {
        return $this->index;
    }

    public function valid()
    {
        return $this->current instanceof JobPosting;
    }

    public function rewind()
    {
        foreach ($this->agents as $agent) {
            $agent->getIterator()->rewind();
        }

        $this->next();
    }

    /**
     * @return JobPosting|null
     */
    protected function getNextJobPosting()
    {
        $currentCandidates = [];

        foreach ($this->agents as $key => $agent) {
            if (!$agent->getIterator()->valid()) {
                continue;
            }

            $current = $agent->getIterator()->current();

            if (!$current instanceof JobPosting) {
                continue;
            }

            $currentCandidates[$key] = $current;
        }

        if (empty($currentCandidates)) {
            return null;
        }

        // vote
        uasort($currentCandidates, function ($candidateA, $candidateB) {
            /** @var JobPosting $candidateA */
            /** @var JobPosting $candidateB */
            ($candidateA->getPublishedAt() > $candidateB->getPublishedAt()) ? -1 : 1;
        });

        // get winner key (first array key)
        reset($currentCandidates);
        $winnerKey = key($currentCandidates);

        // get winner
        $winner = $currentCandidates[$winnerKey];

        // winner iterator go next
        $this->agents[$winnerKey]->getIterator()->next();

        return $winner;
    }

    /**
     * @param AgentInterface $agent
     * @return $this
     */
    public function addAgent(AgentInterface $agent)
    {
        $this->agents[$agent->getName()] = $agent;

        return $this;
    }
}
