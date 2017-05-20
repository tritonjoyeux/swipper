<?php

namespace Fashiongroup\Swiper\Agents\Zhaopin;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\Model\Address;
use Fashiongroup\Swiper\Model\Country;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use Fashiongroup\Swiper\Search;

/**
 * Class ZhaopinAgent
 * @package Fashiongroup\Swiper\Agents\Zhaopin
 */
class ZhaopinAgent extends AbstractAgent
{
    /**
     * @var Session
     */
    private $session;


    /**
     * IndeedAgent constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param array $cursor
     * @return JobPostingResultSet
     */
    public function search($cursor = [])
    {
        $defaultCursor = [
            'start' => 1
        ];

        $cursor = array_merge($defaultCursor, $cursor);

        $searchUrl = $this->getListUrl($this->search, $cursor);
        $this->session->visit($searchUrl);
        $page = $this->session->getPage();

        $elements = $page->findAll('css', '.newlist');

        $jobPostingCollection = new ArrayCollection();

        foreach ($elements as $i => $element) {
            if ($i == 0) {
                continue;
            }

            $companyElement = $element->find('css', '.gsmc a');
            $jobPosting = $this->createJobPosting();

            if ($companyElement) {
                $organization = new Organization($companyElement->getText());
                $jobPosting->setHiringOrganization($organization);
                $organization->setDescription($companyElement->getAttribute('href'));
            }

            $jobTitle = $element->find('css', '.zwmc > div > a');

            if (!$jobTitle) {
                continue;
            }

            $jobLink    = $jobTitle->getAttribute('href');
            $date       = new \DateTime(date('Y') . '-' . $element->find('css', '.gxsj')->getText(), new \DateTimeZone('Asia/Shanghai'));
            $baseSalary = $element->find('css', '.zwyx');

            $jobPosting
                ->setTitle($jobTitle->getText())
                ->setBaseSalary($baseSalary->getText())
                ->setPublishedAt($date)
                ->setUrl($jobLink);

            $jobPostingCollection->add($jobPosting);
        }

        // hasNextPage
        $hasNextResultSet = !is_null($page->find('css', '.pagesDown-pos a'));

        $nextCursor = $hasNextResultSet ? [
            'start' => $cursor['start'] + 1
        ] : false;

        return new JobPostingResultSet($jobPostingCollection, $nextCursor);
    }

    /**
     * @param JobPosting $jobPosting
     * @return JobPosting
     */
    public function refine(JobPosting $jobPosting)
    {
        $this->session->visit($jobPosting->getUrl());
        $page = $this->session->getPage();

        $source = $page->find('css', '.terminalpage-left')->getOuterHtml();
        preg_match('#<!-- SWSStringCutStart -->(.*?)<!-- SWSStringCutEnd -->#si', $source, $description);

        $existingOrganization = $jobPosting->getHiringOrganization();

        $organization = $existingOrganization ? $existingOrganization : new Organization();
        $organization->setDescription($this->getSocietyInfo($source));

        return $jobPosting
            ->setDescription($description[1])
            ->setJobLocation($this->getJobAddress($source))
            ->setExperience($this->getJobExperience($source))
            ->setEmploymentType(1);
    }

    /**
     * @param $source
     * @return mixed
     */
    private function getSocietyInfo($source)
    {
        preg_match('#<div class="tab-inner-cont" .*?>.*?<h5>.*?</h5>(.*?)</div#si', $source, $presentation);
        return $presentation[1];
    }

    /**
     * @param $source
     * @return mixed
     */
    private function getJobAddress($source)
    {
        preg_match('#<b>工作地址：</b>.*?<h2>(.*?)</h2>#si', $source, $adresse);
        return $adresse[1];
    }

    /**
     * @param $source
     * @return mixed
     */
    private function getJobExperience($source)
    {
        preg_match('#<span>工作经验：</span><strong>(.*?)</strong>#si', $source, $experience);
        return $experience[1];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'zhaopin';
    }

    /**
     * @return string
     */
    protected function getListBaseUrl()
    {
        return 'http://sou.zhaopin.com/jobs/searchresult.ashx';
    }

    /**
     * @param Search $search
     * @param $cursor
     * @return string
     */
    protected function getListUrl(Search $search, $cursor)
    {
        return $this->getListBaseUrl() . '?' . http_build_query([
                'jl' => $search->getLocation(),
                'kw' => $search->getTerms(),
                'p'  => $cursor['start']
            ]);
    }
}
