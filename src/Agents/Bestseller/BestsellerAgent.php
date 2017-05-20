<?php

namespace Fashiongroup\Swiper\Agents\Bestseller;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\FormatterLibrary\Formatter;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use Fashiongroup\Swiper\Search;

class BestsellerAgent extends AbstractAgent
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function search($cursor = [])
    {
        $collection = new ArrayCollection();

        $cursorDefault = [
            "page" => 1
        ];

        $cursor = array_merge($cursorDefault, $cursor);

        $url = $this->getListUrl($this->getSearch(), $cursor);

        $this->session->visit($url);
        $page = $this->session->getPage();

        $annonceElements = $page->findAll('css', '.box.job.has-bodytext');

        foreach ($annonceElements as $element) {
            $jobPosting = $this->createJobPosting();

            $linkElement = $element->find('css', '.box-link');
            $link = $linkElement ? $this->session->getLinkUrl($linkElement) : null;

            $titleElement = $element->find('css', '.headline');
            $title = $titleElement ? $titleElement->getText() : null;

            $locationElement = $element->find('css', '.location');
            $location = $locationElement ? str_replace("\r", "", str_replace('Location:', '', $locationElement->getText())) : null;

            $organizationElement = $element->find('css', '.pre-heading');
            $organization = $organizationElement ? new Organization($organizationElement->getText()) : null;

            //$dateElement = $element->find('css', 'time');

            $collection->add($jobPosting
                ->setUrl($link)
                ->setHiringOrganization($organization)
                ->setPublishedAt(new \DateTime())
                ->setJobLocation($location)
                ->setTitle($title)
            );
        }

        $nextCursor = $cursor;
        $nextCursor["page"]++;

        if (!$this->hasNextResult($collection)) {
            $nextCursor = false;
        }

        return new JobPostingResultSet($collection, $nextCursor);
    }

    public function hasNextResult(ArrayCollection $collection)
    {
        return $collection->count() !== 0;
    }

    public function refine(JobPosting $jobPosting)
    {
        $this->session->visit($jobPosting->getUrl());

        $page = $this->session->getPage();

        $descriptionElement = $page->find('css', '.inner-body');
        $description = $descriptionElement ? $this->parseDescription($descriptionElement->getOuterHtml()) : null;

        $linkElement = $page->find('css', 'a.box-link.distance');
        $link = $linkElement ? $linkElement->getAttribute('href') : null;

        return $jobPosting
            ->setDescription($description)
            ->setUrl($link);
    }

    private function parseDescription($content)
    {
        $formatter = new Formatter();
        $description = preg_replace('/About JACK & JONES.*/', '', $content);
        $description = preg_replace("/\\r\\n/", '', $description);
        $description = preg_replace('/<div>/', "\n", $description);
        $description = preg_replace('/<\/ul>/', "\n", $description);
        $description = preg_replace('/<\/li>/', "\n", $description);
        $description = preg_replace('/<\/ul>/', "\n", $description);
        $description = preg_replace('/<li>/', "- ", $description);
        $description = preg_replace('/•/', "\n•", $description);
        $description = $formatter->reduceLineBreak(html_entity_decode($description));
        return $description;
    }

    private function getListUrl(Search $search, $cursor)
    {
        if ($search->getExtra("brand") == "Vero Moda") {
            return "http://about.bestseller.com/jobs/job-search?search=true&branch=&category=&country=18792&brand=18742&region=&page=" . $cursor["page"];
        } else if ($search->getExtra("brand") == "Jack & Jones") {
            return "http://about.bestseller.com/jobs/job-search?search=true&branch=&category=&country=18792&brand=18754&region=&page=" . $cursor["page"];
        } else if ($search->getExtra("brand") == "Only") {
            if ($search->getCountry() == "de") {
                return "http://about.bestseller.com/jobs/job-search?search=true&branch=&category=&country=18792&brand=18744&region=&page=" . $cursor["page"];
            } else if ($search->getCountry() == "at") {
                return "http://about.bestseller.com/jobs/job-search?search=true&branch=&category=&country=18795&brand=18744&region=&page=" . $cursor["page"];
            }
        }
        throw new \Exception("Brand or country missing");
    }

    public function getName()
    {
        return 'bestseller';
    }
}