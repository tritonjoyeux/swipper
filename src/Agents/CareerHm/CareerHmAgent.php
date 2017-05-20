<?php

namespace Fashiongroup\Swiper\Agents\CareerHm;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\Model\EmploymentTypeEnum;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use Fashiongroup\Swiper\Rss\RssParser;

class CareerHmAgent extends AbstractAgent
{
    private $rss;
    private $session;

    public function __construct(RssParser $rss, Session $session)
    {
        $this->rss = $rss;
        $this->session = $session;
    }

    public function search($cursor = [])
    {
        $collection = new ArrayCollection();

        $flux = $this->rss->setUrlXml($this->getListUrl())->fetch();

        foreach ($flux->channel->item as $item) {
            $jobPosting = $this->createJobPosting();

            $link = $item->link->__toString();
            $title = $item->title->__toString();
            $content = $item->description->__toString();
            $date = new \DateTime($item->pubDate->__toString());

            $description = $this->parseContent($content);

            $collection->add(
                $jobPosting
                    ->setUrl($link)
                    ->setTitle($title)
                    ->setPublishedAt($date)
                    ->setDescription($description)
                    ->setHiringOrganization(new Organization("H&M"))
            );
        }

        $collection = $this->sortCollection($collection);

        return new JobPostingResultSet($collection, false);
    }

    public function refine(JobPosting $jobPosting)
    {
        $this->session->visit($jobPosting->getUrl());

        $page = $this->session->getPage();

        $content = $page->find('css', '.hmtext.parbase.hmkeyvaluetext.section');

        $content = $content ? $content->getOuterHtml() : null;

        $skills = null;

        $content = str_replace("\r\n", "", strip_tags(str_replace("<br>", "\n", $content)));

        if ($content) {
            preg_match('/PERFIL:(.*?)PRINCIPALES ACTIVIDADES A DESARROLLAR/is', $content, $matches);
            $skills = isset($matches[1]) ? $matches[1] : null;
        }

        $employementTypeElement = $page->findAll('css', '.job-form .job-form-fields .clearfix dd');

        $employementType = $employementTypeElement[0] ? $this->tradEmployementType($employementTypeElement[0]->getText()) : null;
        $location = $employementTypeElement[1] ? $employementTypeElement[1]->getText() : null;

        if ($employementType == "FULL_TIME") {
            $employementType = EmploymentTypeEnum::FULL_TIME;
        }

        if ($employementType == "PART_TIME") {
            $employementType = EmploymentTypeEnum::PART_TIME;
        }

        return $jobPosting
            ->setEmploymentType($employementType)
            ->setJobLocation($location)
            ->setSkills($skills);
    }

    private function tradEmployementType($employementType)
    {
        $trad = [
            "Tiempo completo" => "FULL_TIME",
            "Tiempo parcial" => "PART_TIME"
        ];

        return isset($trad[$employementType]) ? $trad[$employementType] : null;
    }

    public function sortCollection(ArrayCollection $collection)
    {
        $iterator = $collection->getIterator();
        $iterator->uasort(function ($first, $second) {
            if ($first === $second) {
                return 0;
            }
            return $first->getPublishedAt() > $second->getPublishedAt() ? -1 : 1;
        });
        return new ArrayCollection(iterator_to_array($iterator));
    }

    private function parseContent($content)
    {
        $content = strip_tags($content);
        $content = preg_replace('/.*PRINCIPALES ACTIVIDADES A DESARROLLAR/s', '', $content);
        $description = str_replace("\n", "", $content);
        return html_entity_decode($description);
    }

    public function getName()
    {
        return "career_hm";
    }

    public function getListUrl()
    {
        return "http://career.hm.com/content/hmcareer/es_mx/findjob.rssjob2.xml?_charset_=utf-8&category=all&region=all&city=all&type=all&path=/content/hmcareer/es_mx&q=&locale=es_MX&resource=%252fcontent%252fhmcareer%252fes_mx%252ffindjob%252fjcr%253acontent%252fpar%252fsearchjobad";
    }

}