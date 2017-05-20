<?php

namespace Fashiongroup\Swiper\Agents\Hh;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\Model\Contact;
use Fashiongroup\Swiper\Model\EmploymentTypeEnum;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use Fashiongroup\Swiper\Search;

class HhAgent extends AbstractAgent
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
            "start" => 1,
            "nbResults" => null
        ];

        $cursor = array_merge($cursorDefault, $cursor);

        $url = $this->getListUrl($this->search, $cursor);

        $this->session->visit($url);
        $page = $this->session->getPage();

        $checkElements = [
            "first" => $page->find('css', '.navi-logo.navi-logo_hh-ua'),
            "second" => $page->find('css', '.bloko-input'),
            "third" => $page->find('css', '.navi-link')
        ];
        $this->isModified($checkElements);

        $nbResultsElement = $page->find('css', '.resumesearch__result-count');
        $nbResultsText = $nbResultsElement ? $nbResultsElement->getText() : null;
        $nbResults = filter_var($nbResultsText, FILTER_SANITIZE_NUMBER_INT);
        $cursor['nbResults'] = $nbResults;

        $collection = new ArrayCollection();

        $rows = $page->findAll('css', '.search-result-item');

        foreach ($rows as $row) {
            $jobPosting = $this->createJobPosting();

            $linkElement = $row->find('css', '.search-result-item__head a');

            $link = $linkElement ? $linkElement->getAttribute('href') : null;
            $title = $linkElement ? $linkElement->getText() : null;

            $hiringOrganizationElement = $row->find('css', '.bloko-link.bloko-link_secondary');

            if ($hiringOrganizationElement) {
                $jobPosting->setHiringOrganization(new Organization($hiringOrganizationElement->getText()));
            }

            $publishedAtElement = $row->find('css', '.b-vacancy-list-date');
            $publishedAt = $publishedAtElement ? $this->parseDate($publishedAtElement->getText()) : null;

            $jobPosting
                ->setUrl($link)
                ->setTitle($title)
                ->setPublishedAt($publishedAt);

            $collection->add($jobPosting);
        }

        $nextCursor = [
            "start" => $cursor['start'] + 1,
            "nbResults" => $cursor['nbResults']
        ];

        if ($this->hasNextResults($nextCursor)) {
            $nextCursor = false;
        }

        return new JobPostingResultSet($collection, $nextCursor);
    }

    protected function hasNextResults($cursor)
    {
        return $cursor['start'] * 20 > $cursor["nbResults"];
    }

    public function refine(JobPosting $jobPosting)
    {
        $this->session->visit($jobPosting->getUrl());
        $contact = new Contact();
        $page = $this->session->getPage();

        $cityElement = $page->find('css', '.b-vacancy-info td:nth-child(2)');
        $city = $cityElement ? $cityElement->getText() : null;

        $descriptionElement = $page->find('css', '.b-vacancy-desc-wrapper');
        $description = $descriptionElement ? $descriptionElement->getText() : null;

        $contactInformationElement = $page->find('css', '.vacancy-contacts__body');

        $contactEmailElement = $contactInformationElement ? $contactInformationElement->find('css', 'a') : null;
        $contactEmailElement ? $contact->setEmail($contactEmailElement->getText()) : null;

        $contactPhoneElement = $contactInformationElement ? $contactInformationElement->find('css', '.vacancy-contacts__phone') : null;
        $contactPhoneElement ? $contact->setPhone($contactPhoneElement->getText()) : null;

        $employementTypeElement = $page->find('css', '.b-vacancy-employmentmode.l-paddings');
        $employementTypeText = $employementTypeElement ? $employementTypeElement->getText() : null;
        $employementType = strpos($employementTypeText, 'полный день') ? EmploymentTypeEnum::FULL_TIME : EmploymentTypeEnum::PART_TIME;

        return $jobPosting
            ->setJobLocation($city)
            ->setEmploymentType($employementType)
            ->setDescription($description)
            ->setContact($contact);
    }

    public function getName()
    {
        return 'hh';
    }

    protected function getListUrl(Search $search, $cursor)
    {
        $queryParams = [
            'enable_snippets' => 'true',
            'order_by' => 'publication_time',
            'clusters' => 'true',
            'currency_code' => 'UAH',
            'text' => $search->getTerms(),
            'area' => $this->getHHAreaId($search),
            'from' => 'cluster_area'
        ];

        if ($cursor['start'] !== 1) {
            $queryParams['page'] = $cursor['start'];
        }

        return 'https://hh.ua/search/vacancy?' . http_build_query($queryParams);
    }

    protected function getHHAreaId(Search $search)
    {
        $areas = [
            'ua' => 5,
            'ru' => 113
        ];

        return isset($areas[$search->getCountry()]) ? $areas[$search->getCountry()] : null;
    }

    private function parseDate($string)
    {
        $months = [
            'января' => 1,
            'февраля' => 2,
            'марта' => 3,
            'апреля' => 4,
            'мая' => 5,
            'июня' => 6,
            'июля' => 7,
            'августа' => 8,
            'сентября' => 9,
            'октября' => 10,
            'ноября' => 11,
            'декабря' => 12
        ];

        $matches = [];
        preg_match('/(\d+)\s(\w+)/u', $string, $matches);

        $monthPosition = $months[$matches[2]];

        // guess year
        $year = date('Y') - ($monthPosition <= date('n') ? 0 : 1);

        return new \DateTime(sprintf('%d-%d-%d', $year, $monthPosition, $matches[1]));
    }
}
