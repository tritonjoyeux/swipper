<?php

namespace Fashiongroup\Swiper\Agents;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\AgentSearchIterator;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\MultipleAgentSearchIterator;
use Fashiongroup\Swiper\Search;

abstract class CompositeAgent extends AbstractAgent
{
    /**
     * @var AgentInterface[]
     */
    private $agents = [];

    public function search($cursor = [])
    {
        throw new \Exception('composite agent does no implement search method');
    }

    /**
     * @param $cursor
     * @return bool
     */
    protected function hasNextResultSet($cursor)
    {
        foreach ($cursor as $cursorElement) {
            if ($cursorElement !== false) {
                return true;
            }
        }

        return false;
    }

    public function refine(JobPosting $jobPosting)
    {
        return $this->getAgent($jobPosting->getSource())->refine($jobPosting);
    }

    public function support(Search $search)
    {
        foreach ($this->getAgents() as $agent) {
            if ($agent->support($search)) {
                return true;
            }
        }

        return false;
    }

    public function addAgent(AgentInterface $agent)
    {
        $this->agents[$agent->getName()] = $agent;

        return $this;
    }

    public function getAgents()
    {
        return $this->agents;
    }

    public function getAgent($agentName)
    {
        if (isset($this->agents[$agentName])) {
            return $this->agents[$agentName];
        }

        foreach ($this->agents as $agent) {
            if ($agent instanceof CompositeAgent) {
                $result = $agent->getAgent($agentName);

                if ($result) {
                    return $result;
                }
            }
        }

        return null;
    }

    public function getIterator()
    {
        $iterator = new MultipleAgentSearchIterator();
        foreach ($this->agents as $agent) {
            if (!$agent->support($this->search)) {
                continue;
            }

            $agent->setSearch($this->search);
            $iterator->addAgent($agent);
        }

        return $iterator;
    }
}
