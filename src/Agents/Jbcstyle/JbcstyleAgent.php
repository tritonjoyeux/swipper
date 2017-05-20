<?php

namespace Fashiongroup\Swiper\Agents\Jbcstyle;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\FormatterLibrary\Formatter;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use Fashiongroup\Swiper\Rss\RssParser;

class JbcstyleAgent extends AbstractAgent
{
    private $rss;

    /**
     * IndeedAgent constructor.
     * @param RssParser $rss
     */
    public function __construct(RssParser $rss)
    {
        $this->rss = $rss;
    }

    public function search($cursor = [])
    {
        $collection = new ArrayCollection();
        $formatter = new Formatter();

        $replace = [
            '<br.*?\/>' => "\n",
            'Responsibilities:' => "\nResponsibilities:\n",
            'Title of Job:' => "\nTitle of Job:\n",
            'STRATEGY' => "\nSTRATEGY\n",
            'SOURCING' => "\nSOURCING\n",
            'PRODUCTION MANAGEMENT' => "\nPRODUCTION MANAGEMENT\n",
            'QUALITY' => "\nQUALITY\n",
            'Salary:' => "\nSalary:\n",
            '<\/p>' => "\n"
        ];

        $annonces = $this->rss->setUrlXml($this->getListUrl())->fetch();

        $checkElements = [
            "first" => isset($annonces->job)
        ];
        $this->isModified($checkElements);

        foreach ($annonces->job as $annonce) {
            $jobPosting = $this->createJobPosting();

            $description = $formatter->multipleReplaceBy($annonce->description->__toString(), $replace);
            $description = html_entity_decode($formatter->reduceLineBreak(strip_tags($description)));

            $location = strtolower($annonce->country->__toString()) == 'us' ? $annonce->country->__toString() .' - '. $annonce->city->__toString() : null;
            $url = $annonce->url->__toString();
            $title = $annonce->title->__toString();
            $salary = $annonce->salary->__toString();
            $date = new \DateTime($annonce->date->__toString());
            $organization = new Organization($annonce->company->__toString());

            $jobPosting->setTitle($title)
                ->setDescription($description)
                ->setJobLocation($location)
                ->setUrl($url)
                ->setBaseSalary($salary)
                ->setPublishedAt($date)
                ->setHiringOrganization($organization);

            $collection->add($jobPosting);
        }
        return new JobPostingResultSet($collection, false);
    }

    public function refine(JobPosting $jobPosting)
    {
        return $jobPosting;
    }

    public function getName()
    {
        return 'jbcstyle';
    }

    protected function getListUrl()
    {
        return 'https://careers.haleymarketing.com/xml/xml.smpl?id=101479&pass=style&rid=FashionJobsUSA';
    }
}
