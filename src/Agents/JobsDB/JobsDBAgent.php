<?php

namespace Fashiongroup\Swiper\Agents\JobsDB;

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

class JobsDBAgent extends AbstractAgent
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

        $this->session->visit($this->getListUrl($this->search));

        $page = $this->session->getPage();

        $checkElements = [
            "first" => $page->find('css', '.navbar-brand-logo'),
            "second" => $page->find('css', '.dropdown-toggle'),
            "third" => $page->find('css', '#firstLineCriteriaContainer')
        ];
        $this->isModified($checkElements);

        $rows = $page->findAll('css', '.result-sherlock-table .result-sherlock-cell:not(:last-child)');

        foreach ($rows as $row) {
            $jobPosting = $this->createJobPosting();

            $timestampElement = $row->find('css', '.job-quickinfo meta');
            $date = $timestampElement ? $timestampElement->getAttribute('content') : null;

            $linkElement = $row->find('css', 'a.posLink');
            $link = $linkElement ? $linkElement->getAttribute("href") : null;

            $locationElement = $row->find('css', 'p.job-location');
            $location = $locationElement !== null && $locationElement->getText() !== 'Location not specified' ? /*$locationElement->getText() .*/
                'Hong Kong City - HK' : null;

            $titleElement = $row->find('css', '.job-title');
            $title = $titleElement ? $titleElement->getText() : null;

            $jobPosting
                ->setPublishedAt(new \DateTime($date, new \DateTimeZone('Asia/Shanghai')))
                ->setUrl($link)
                ->setJobLocation($location)
                ->setHiringOrganization(new Organization("Moiselle"))
                ->setTitle($title);


            if ($jobPosting->getJobLocation()) {
                $collection->add($jobPosting);
            }
        }
        return new JobPostingResultSet($collection, false);
    }

    public function refine(JobPosting $jobPosting)
    {
        $formatter = new Formatter();
        $contact = new Contact();

        $this->session->visit($jobPosting->getUrl());
        $page = $this->session->getPage();

        $employementType = $page->find('css', '.meta-employmenttype > p > span')->getText();
        $descriptionElements = $page->findAll('css', '.jobad-primary-details p, .jobad-primary-details ul');
        $experience = $page->find('css', '.primary-meta-exp > span')->getText();
        $baseSalary = $page->find('css', '.primary-meta-salary > span')->getText();
        $content = "";

        foreach ($descriptionElements as $element) {
            if (!strpos(strtolower($element->getText()), "apply now")) {
                $content .= $element->getOuterHtml() . "\r";
            }
        }

        $content = $formatter->reduceLineBreak($content);
        $content = str_replace("Requirements:?", "\r \rRequirements:\r ", $content);
        $content = str_replace("Responsibilities:?", "Responsibilities:\r \r", $content);
        preg_match('#(.*)</ul>.*?equirements:?#si', $content, $matches); //description

        if (count($matches) > 2) {
            $description = $matches[1] . "\n" . $matches[2];
        } else {
            $description = $matches[1];
        }

        preg_match('#equirements:?(.*)#si', $content, $skills); //requirement

        $employementType = strpos($employementType, 'Full Time') === false ? EmploymentTypeEnum::PART_TIME : EmploymentTypeEnum::FULL_TIME;

        $replace = [
            "\\n" => "",
            "(\\r )" => "\r",
            "(\\r)+" => "\r",
            "&nbsp;" => ""
        ];

        $description = $formatter->replaceBy($description, '<\/li>', "\r");
        $description = $formatter->multipleReplaceBy($description, $replace);
        $description = $formatter->replaceByVoid($description, 'Responsibilities:?');
        $description = $formatter->replaceByVoid($description, 'Job description');
        $description = html_entity_decode($description);
        $description = strip_tags($description);
        $description = $formatter->replaceByVoid($description, 'Job Duties:?');
        $description = $formatter->replaceBy($description, '&.{1,5};', '');
        $description = $formatter->replaceBy($description, '       ', "\r");
        $description = $formatter->reduceLineBreak($description);

        $skills = isset($skills[1]) ? $skills[1] : null;

        $skills = $formatter->multipleReplaceBy($skills, $replace);
        $skills = html_entity_decode($skills);
        $skills = $formatter->replaceBy($skills, '&.{1,5};', '');

        $jobPosting
            ->setSkills($skills)
            ->setEmploymentType($employementType)
            ->setDescription(strip_tags($description))
            ->setExperience($experience)
            ->setBaseSalary($baseSalary)
            ->setContact($contact->setEmail("hrdept@moiselle.com.hk"));

        return $jobPosting;
    }

    public function getName()
    {
        return 'jobs_db';
    }

    protected function getListUrl(Search $search)
    {
        return 'http://hk.jobsdb.com/HK/en/Search/FindJobs?JSRV=1&Key=%22Moiselle%22&KeyOpt=COMPLEX&SearchFields=Companies&JSSRC=JDFT';
    }
}
