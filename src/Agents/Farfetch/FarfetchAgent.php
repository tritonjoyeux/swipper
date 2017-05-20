<?php

namespace Fashiongroup\Swiper\Agents\Farfetch;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\FormatterLibrary\Formatter;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use Fashiongroup\Swiper\Search;
use Symfony\Component\DomCrawler\Crawler;

class FarfetchAgent extends AbstractAgent
{
    private $session;

    /**
     * IndeedAgent constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function search($cursor = [])
    {
        $collection = new ArrayCollection();

        $this->session->visit($this->getListUrl());
        $page = $this->session->getPage();

        $checkElements = [
            "first" => $page->find('css', '.jv-logo'),
            "second" => $page->find('css', '.jv-wrapper'),
            "third" => $page->find('css', '.jv-page-body')
        ];

        $this->isModified($checkElements);

        $secteursElements = $page->findAll('css', '.jv-job-list');

        foreach ($secteursElements as $secteursElement) {

            $annoncesList = $secteursElement->findAll('css', 'tr');

            foreach ($annoncesList as $annonce) {
                $jobPosting = $this->createJobPosting();

                $urlElement = $annonce->find('css', 'a');
                $locationElement = $annonce->find('css', '.jv-job-list-location');
                $location = $locationElement ? $locationElement->getText() : null;

                $url = $urlElement ? 'http://jobs.jobvite.com/' . $urlElement->getAttribute('href') : null;
                $title = $urlElement ? $urlElement->getText() : null;

                $collection->add(
                    $jobPosting
                        ->setUrl($url)
                        ->setTitle($title)
                        ->setHiringOrganization(new Organization('farfetch'))
                        ->setPublishedAt(new \DateTime())
                        ->setJobLocation($location)
                );
            }
        };

        // filter jobPosting if his location is not supported
        $collection = $collection->filter(function($jobPosting) {
            $supportedLocations = $this->getSupportedLocations();

            foreach ($supportedLocations as $supportedLocation) {
                if (stripos($jobPosting->getJobLocation(), $supportedLocation) !== false) {
                    return true;
                }
            }

            return false;
        });

        return new JobPostingResultSet($collection, false);
    }

    protected function getSupportedLocations() {
        return [
            'porto',
            'guimarães',
            'lisbon'
        ];
    }

    public function refine(JobPosting $jobPosting)
    {
        $tabContent = [
            'description' => null,
            'skills' => null
        ];

        $this->session->visit($jobPosting->getUrl());
        $page = $this->session->getPage();

        $contentElements = $page->findAll('css', '.jv-job-detail-description > *:not(:first-child)');

        $content = "";
        foreach ($contentElements as $contentElement) {
            $content .= $contentElement->getOuterHtml();
        }

        if ($content) {
            $tabContent = $this->parseDescription(html_entity_decode($content));
        }

        return $jobPosting->setDescription($tabContent['description'])->setSkills($tabContent['skills']);
    }

    public function getName()
    {
        return 'farfetch';
    }

    protected function getListUrl()
    {
        return 'http://jobs.jobvite.com/farfetch/jobs';
    }

    protected function parseDescription($content)
    {
        $formatter = new Formatter();

        $replace = [
            'Responsibilities' => "",
            '<h4>' => "\r\r",
            '<\/li>' => "\r",
            '<\/ul>' => "\r"
        ];

        $description = $formatter->multipleReplaceBy($content, $replace);

        return ['description' => $description, 'skills' => null];
    }
}
