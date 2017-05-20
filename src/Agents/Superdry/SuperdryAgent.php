<?php

namespace Fashiongroup\Swiper\Agents\Superdry;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\FormatterLibrary\Formatter;
use Fashiongroup\Swiper\Model\EmploymentTypeEnum;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use Fashiongroup\Swiper\Rss\RssParser;

class SuperdryAgent extends AbstractAgent
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
        $annonces = $this->rss->setUrlXml($this->getListUrl())->fetch();

        $checkElements = [
            "first" => isset($annonces->channel->item)
        ];
        $this->isModified($checkElements);

        foreach ($annonces->channel->item as $annonce) {
            $jobPosting = $this->createJobPosting();

            $title = $annonce->title->__toString();

            //$location = preg_replace('/\\n.*/s', "", $annonce->storeAddress->__toString());
            $location = $annonce->storeCountry->__toString() . ' - ' . $annonce->storePostcode->__toString();

            $organezation = new Organization($annonce->storeName->__toString());
            $type = strtolower($annonce->hours_english->__toString()) == "full time" ? EmploymentTypeEnum::FULL_TIME : EmploymentTypeEnum::PART_TIME;
            $description = $annonce->fullJobDescription->__toString();
            $url = "www.careers.superdry.com" . $annonce->link->__toString();
            $date = new \DateTime($annonce->postdate->__toString());

            $jobPosting->setTitle($title)
                ->setJobLocation($location)
                ->setHiringOrganization($organezation)
                ->setEmploymentType($type)
                ->setDescription(str_replace("<li>", "- ", $description))
                ->setUrl($url)
                ->setPublishedAt($date);

            if (strtolower($annonce->storeCountry->__toString()) == 'united states' || strtolower($annonce->storeCountry->__toString()) == 'united kingdom') {
                $collection->add($jobPosting);
            }
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
        return 'superdry';
    }

    protected function getListUrl()
    {
        return 'http://careers.superdry.com/recruitment_uk/rssout.php';
    }
}
