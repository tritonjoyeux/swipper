<?php

namespace Fashiongroup\Swiper\Agents\FiftyEight;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Search;

class FiftyEightAgent extends AbstractAgent
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function search($cursor = [])
    {
        $cursorDefault = [
            "page" => 1
        ];

        $cursor = array_merge($cursorDefault, $cursor);

        $collection = new ArrayCollection();

        $url = $this->getListUrl($this->getSearch(), $cursor);

        $this->session->visit($url);
        $page = $this->session->getPage();

        $elements = $page->findAll('css', '.tblist tr');

        foreach ($elements as $element) {
            $jobPosting = $this->createJobPosting();

            $linkElement = $element->find('css', '.t a');
            $link = $linkElement ? $linkElement->getAttribute('href') : null;
            $title = $linkElement ? $linkElement->getText() : null;

            $dateElement = $element->find('css', '.abt');
            $date = $dateElement ? $dateElement->getText() : null;
            preg_match('/.*?(\d+-\d+)/', $date, $matches);

            $collection->add(
                $jobPosting
                    ->setUrl($link)
                    ->setTitle($title)
                    ->setPublishedAt(new \DateTime())
            );
        }

        $cursor["page"] = $collection->count() == 0 ? false : $cursor["page"] + 1;
        if($cursor["page"] == false){
            $cursor = false;
        }

        return new JobPostingResultSet($collection, $cursor);
    }

    public function refine(JobPosting $jobPosting)
    {
        $this->session->visit($jobPosting->getUrl());
        $page = $this->session->getPage();

        /*$dateElement = */
        return $jobPosting;
    }

    public function getName()
    {
        return "58";
    }

    private function getListUrl(Search $search, $cursor)
    {
        return "http://" . $search->getCountry() . ".58.com/sou/pn" . $cursor["page"] . "/?key=" . $search->getTerms();
    }
}