<?php

namespace Fashiongroup\Swiper\Agents\Biba;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\FormatterLibrary\Formatter;
use Fashiongroup\Swiper\Model\Contact;
use Fashiongroup\Swiper\Model\EmploymentTypeEnum;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use Fashiongroup\Swiper\Search;

class BibaAgent extends AbstractAgent
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
            "first" => $page->find('css', '#customerbox'),
            "second" => $page->find('css', '#secondnavigation'),
            "third" => $page->find('css', '#logo')
        ];

        $this->isModified($checkElements);

        $nodeElements = $page->findAll('css', '.accordion');

        foreach($nodeElements as $element){
            $jobPosting = $this->createJobPosting();

            $titleElement = $element->find('css', '.attribute');

            $location = null;
            $title = null;

            if($titleElement){
                preg_match('/<small>(.*)<\/small>/', $titleElement->getOuterHtml(), $matches);
                if(isset($matches[1])){
                    $location = strip_tags($matches[1]);
                }
                preg_match('/<span class="attribute">(.*)<small>/', $titleElement->getOuterHtml(), $matches);
                if(isset($matches[1])){
                    $title = strip_tags($matches[1]);
                }
            }

            $descriptionElement = $element->find('css', '.more-info');
            $description = $descriptionElement ? $descriptionElement->getText() : null;
            $description = str_replace("Sie bringen mit:", "\nSie bringen mit:", $description);
            $description = str_replace("Wir bringen mit:", "\nWir bringen mit:", $description);
            $description = str_replace("Bitte richten", "\nBitte richten", $description);

            $contact = new Contact();

            $urlElement = $element->find('css', '.btn.btn-primary.btn-block');
            $url = $urlElement ? $urlElement->getAttribute('href') : null;

            $jobPosting
                ->setTitle($title)
                ->setJobLocation($location)
                ->setDescription($description)
                ->setHiringOrganization(new Organization("Biba"))
                ->setPublishedAt(new \DateTime())
                ->setUrl($url)
                ->setContact($contact->setEmail("bewerbung@biba.de"));

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
        return 'biba';
    }

    protected function getListUrl()
    {
        return 'https://www.biba.de/index.php?cl=sm_career_controller';
    }
}
