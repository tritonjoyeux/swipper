<?php

namespace Fashiongroup\Swiper\Agents\Moiselle;

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

class MoiselleAgent extends AbstractAgent
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
            "first" => $page->find('css', '.logo.col-lg-4.col-md-3.col-sm-4'),
            "second" => $page->find('css', '#search'),
            "third" => $page->find('css', '.box-header-01')
        ];
        $this->isModified($checkElements);

        $links = $page->findAll('css', 'div.carrers_block ul li a');
        foreach ($links as $key => $link) {
            $jobPosting = $this->createJobPosting();
            $jobPosting->setUrl($link->getAttribute('href'))
                ->setTitle($link->getText())
                ->setJobLocation('Hong Kong City - HK')
                ->setHiringOrganization(new Organization("Moiselle"))
                ->setPublishedAt(new \DateTime())
                ->setEmploymentType(EmploymentTypeEnum::FULL_TIME);

            $collection->add($jobPosting);
        }

        return new JobPostingResultSet($collection, false);
    }

    public function refine(JobPosting $jobPosting)
    {
        $formatter = new Formatter();
        $this->session->visit($jobPosting->getUrl());
        $page = $this->session->getPage();

        if ($page->find('css', '.carrers_desc_block a') !== null) {
            $contact = new Contact();
            $jobPosting->setContact($contact->setEmail($page->find('css', '.carrers_desc_block a')->getText()));
        }

        $lists = $page->findAll(
            'css',
            '.carrers_desc_block > *:not(.name):not(:last-child)'
        );

        $content = "";
        foreach ($lists as $list) {
            $content .= $list->getText();
        }
        preg_match('/(.*)Requirements:.*?/', $content, $description); //description
        $description = isset($description[1]) ? $description[1] : null;

        if ($description) {
            preg_match('/Requirements:(.*?)Please attach your cover letter/', $content, $skills); //requirement
            $skills = isset($skills[1]) ? $skills[1] : null;
        } else {
            preg_match('/(.*?)Please attach your cover letter/', $content, $skills); //requirement
            $skills = isset($skills[1]) ? $skills[1] : null;
        }

        preg_match('/(Please attach your cover letter.*)/', $content, $skillsLastSentence);
        $skills .= isset($skillsLastSentence[1]) ? "\r\r".$skillsLastSentence[1] : null;

        $description = $formatter->reduceLineBreak($description);
        $description = $formatter->replaceByVoid($description, 'Job Description:');
        $description = $formatter->replaceByVoid($description, 'Responsibilities:');

        $skills = $formatter->replaceByVoid($skills, 'Requirements:');
        $skills = $formatter->reduceLineBreak($skills);

        return $jobPosting
            ->setDescription($description)
            ->setSkills($skills)
            ->setEmploymentType(EmploymentTypeEnum::FULL_TIME);
    }

    public function getName()
    {
        return 'moiselle';
    }

    protected function getListUrl()
    {
        return 'http://www.moiselle.com.hk/careers';
    }
}
