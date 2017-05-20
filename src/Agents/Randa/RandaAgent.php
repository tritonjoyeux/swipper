<?php

namespace Fashiongroup\Swiper\Agents\Randa;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\FormatterLibrary\Formatter;
use Fashiongroup\Swiper\Model\EmploymentTypeEnum;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use Fashiongroup\Swiper\Rss\RssParser;
use Fashiongroup\Swiper\Search;

class RandaAgent extends AbstractAgent
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

        $annonces = simplexml_load_string(file_get_contents('https://recruiting.adp.com/srccsh/public/ws_req_feed.guid?c=1101341&d=ExternalCareerSite&f=???&o=true'));

        $checkElement = [
            "first" => isset($annonces->JOB)
        ];

        $this->isModified($checkElement);

        foreach ($annonces->JOB as $annonce) {
            $jobPosting = $this->createJobPosting();
            $employementType = $annonce->FULLTIME == 'Full-time' ? EmploymentTypeEnum::FULL_TIME : EmploymentTypeEnum::PART_TIME;
            $description = $annonce->JOBBODY->__toString();

            $skills = null;

            preg_match('/(.*)Job Requirements:(.*)/', $description, $matches);

            if (isset($matches[2])) {
                //dump($matches[1], $matches[2]);
                //$skills = $matches[2];
                //$description = $matches[1];
            }

            $jobPosting->setTitle($annonce->JOBTITLE->__toString())
                ->setDescription($formatter->reduceLineBreak($description))
                ->setJobLocation($annonce->JOBLOCATION->__toString())
                ->setUrl($annonce->JOBLINK->__toString())
                ->setPublishedAt(new \DateTime($annonce->OPENDATE->__toString()))
                ->setHiringOrganization(new Organization($annonce->COMPANY->__toString()))
                ->setEmploymentType($employementType)
                ->setSkills($skills);
            $collection->add($jobPosting);
        }
        $collection = $this->sortCollection($collection);
        return new JobPostingResultSet($collection, false);
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

    public function refine(JobPosting $jobPosting)
    {
        return $jobPosting;
    }

    public function getName()
    {
        return 'randa';
    }

    protected function getListUrl()
    {
        return 'https://recruiting.adp.com/srccsh/public/ws_req_feed.guid?c=1101341&d=ExternalCareerSite&f=???&o=true';
    }
}
