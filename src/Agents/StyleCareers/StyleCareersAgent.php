<?php

namespace Fashiongroup\Swiper\Agents\StyleCareers;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Contact;
use Fashiongroup\Swiper\Model\Organization;

class StyleCareersAgent extends AbstractAgent
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
        $cursorDefault = [
            'start' => 0
        ];

        $cursor = array_merge($cursorDefault, $cursor);

        $url = $this->getListUrl($cursor);

        $this->session->visit($url);

        $page = $this->session->getPage();

        $checkElements = [
            "first" => $page->find('css', '.sc-menu-wrap'),
            "second" => $page->find('css', '.pink-menu-link'),
            "third" => $page->find('css', '#main_tab')
        ];

        $this->isModified($checkElements);

        $rows = $page->findAll('css', 'tbody > tr');    //toute les offres

        $collection = new ArrayCollection();

        foreach ($rows as $row) {
            preg_match('/Posted - (\w+) (\d+), (\d+)/', $row->find('css', '.date')->getText(), $matches);
            $date = new \DateTime($matches[2] . ' ' . $matches[1] . ' ' . $matches[3]);

            $jobPosting = $this->createJobPosting()
                ->setTitle($row->find('css', 'a.search')->getText())
                ->setUrl('http://stylecareers.com/' . $row->find('css', 'td > a')->getAttribute('href'))
                ->setPublishedAt($date)
                ->setJobLocation($row->find('css', 'span.search_results_location')->getText())
                ->setHiringOrganization(new Organization($row->find('css', 'div.info > a')->getText()));

            $collection->add($jobPosting);
        }

        $matches = [];
        preg_match(
            '/This company has (\d+) jobs posted/',
            $page->find('css', '#col2 h2:nth-child(2)')->getText(),
            $matches
        );
        $nbResults = (int)$matches[1];

        $nextCursor = [
            'start' => $cursor['start'] + 25,
            'nbResults' => $nbResults
        ];

        if (!$this->hasNextResultSet($nextCursor)) {
            $nextCursor = false;
        }

        return new JobPostingResultSet($collection, $nextCursor);
    }

    protected function hasNextResultSet($cursor)
    {
        return $cursor['nbResults'] >= $cursor['start'];
    }

    public function refine(JobPosting $jobPosting)
    {
        $contact = new Contact();

        $this->session->visit($jobPosting->getUrl());
        $page = $this->session->getPage();


        $jobLocation = join(' - ', [
            $page->find('css', '.view_job .row:nth-child(3) .left2') ? $page->find('css', '.view_job .row:nth-child(3) .left2')->getText() : null,
            $page->find('css', '.view_job .row:nth-child(4) .right2') ? $page->find('css', '.view_job .row:nth-child(4) .right2')->getText() : null
        ]);

        $jobPosting
            ->setDescription($page->find('css', '.view_long') ? $page->find('css', '.view_long')->getText() : null)
            ->setJobLocation($jobLocation);

        return $jobPosting->setContact($contact->setEmail("joe@jaralinc.com"));
    }

    public function getName()
    {
        return 'style_careers';
    }

    protected function getListUrl($cursor)
    {
        return 'http://stylecareers.com/view-employer/employer_id_seo/jaral-fashions-5925/start/' . $cursor['start'];
    }
}
