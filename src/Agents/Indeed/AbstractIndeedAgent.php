<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

use Behat\Mink\Element\NodeElement;
use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use Fashiongroup\Swiper\Search;

abstract class AbstractIndeedAgent extends AbstractAgent
{
    const STEP_PAUSE = 1000;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var DateParser
     */
    private $dateParser;

    /**
     * IndeedAgent constructor.
     * @param Session $session
     * @param DateParser $dateParser
     */
    public function __construct(Session $session, DateParser $dateParser = null)
    {
        $this->session = $session;
        $this->dateParser = $dateParser;

        if ($dateParser) {
            $this->dateParser = $dateParser;
        } else {
            $this->dateParser = new DateParser();

            $this->dateParser
                ->setPattern($this->getDatePattern())
                ->setQuantityTypeMap($this->getDateQuantityTypeMap());
        }
    }

    public function search($cursor = [])
    {
        $defaultCursor = [
            'start' => 0
        ];

        $cursor = array_merge($defaultCursor, $cursor);

        $searchUrl = $this->getListUrl($this->search, $cursor);

        $this->session->visit($searchUrl);
        $page = $this->session->getPage();


        $elements = $page->findAll('css', '.row.result');

        $jobPostingCollection = new ArrayCollection();

        foreach ($elements as $element) {
            /** @var NodeElement $element */

            $companyElement = $element->find('css', '.company');

            $jobPosting = $this->createJobPosting();

            $organization = new Organization();
            if ($companyElement) {
                $organization->setName($companyElement->getText());
            }
            $jobPosting->setHiringOrganization($organization);

            $jobTitle = $element->find('css', '.jobtitle');

            if (!$jobTitle) {
                continue;
            }

            /** @var NodeElement $jobLink */
            $jobLink = $jobTitle->find('css', 'a');

            //extract date
            $date = $element->find('css', '.date');

            if (!$date || !$this->dateParser->parse($date->getText())) {
                continue;
            }

            $jobPosting->setPublishedAt($this->dateParser->parse($date->getText()));

            $locationString = $this->getText($element, '.location');

            $jobPosting
                ->setTitle($jobTitle->getText())
                ->setJobLocation($locationString)
                ->setSummary($this->getText($element, '.summary'))
                ->setUrl($this->session->getLinkUrl($jobLink));

            $jobPostingCollection->add($jobPosting);
        }

        // hasNextPage
        //$hasNextResultSet = !is_null($page->findAll('css', '.pagination a span.pn span.np'));
        $hasNextResultSet = $this->hasNextResult($page);

        $nextCursor = $hasNextResultSet ? [
            'start' => $cursor['start'] + 10
        ] : false;

        return new JobPostingResultSet($jobPostingCollection, $nextCursor);
    }

    public function hasNextResult($page)
    {
        $paginationElements = $page->findAll('css', '.pagination a span.pn span.np');
        foreach ($paginationElements as $paginationElement)
        {
            if($paginationElement->getText() == "Next »"){
                return true;
            }
        }
        return false;
    }

    public function refine(JobPosting $jobPosting)
    {
        // we don't need to refine indeed (metamoteur)

        return $jobPosting;

        /*$this->session->visit($jobPosting->getUrl());

        // update jobPosting url with the resolved one
        $jobPosting->setUrl($this->session->getCurrentUrl());


        // if it's indeed we try to find jobPosting body
        if (strpos($this->session->getCurrentUrl(), 'indeed') === false) {
            return $jobPosting;
        }

        $jobSummary = $this->session->getPage()->findById('job_summary');

        if (!$jobSummary) {
            return $jobPosting;
        }

        return $jobPosting->setDescription($jobSummary->getHtml());*/
    }

    protected function getDatePattern()
    {
        return '/(\d+)\+? ([a-z]+) ago$/';
    }

    protected function getDateQuantityTypeMap()
    {
        return array(
            'day' => 'day',
            'hour' => 'hour',
            'minute' => 'minute'
        );
    }


    public function getText($element, $cssSelector)
    {
        $targetElement = $element->find('css', $cssSelector);

        if (!$targetElement) {
            return;
        }

        return $targetElement->getText();
    }

    public function getName()
    {
        return 'indeed';
    }

    protected function getListUrl(Search $search, $cursor)
    {
        return $this->getListBaseUrl() . '?' . http_build_query([
                'q' => $search->getTerms(),
                'l' => $search->getLocation(),
                'start' => $cursor['start'],
                'sort' => 'date'
            ]);
    }

    protected function getListBaseUrl()
    {
        return 'https://www.indeed.com/jobs';
    }
}
