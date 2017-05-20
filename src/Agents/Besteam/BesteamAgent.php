<?php

namespace Fashiongroup\Swiper\Agents\Besteam;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\Model\Contact;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use Fashiongroup\Swiper\Search;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class BesteamAgent extends AbstractAgent
{
    private $client;

    /**
     * IndeedAgent constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function search($cursor = [])
    {
        $cursorDefault = [
            'start' => 1,
            'nbResults' => 1
        ];

        $cursor = array_merge($cursorDefault,$cursor);

        $params = [
            'ctl00$ContentPlaceHolder1$ucJobSearchResult$btnNav$txtPageNum' => $cursor['start'],
            'ctl00$ContentPlaceHolder1$ucJobSearchResult$btnNav$btnNext' => '',
            'ctl00$ContentPlaceHolder1$ucJobSearchResult$btnNav$hidLastPage' => $cursor['nbResults'],
            'ctl00$ContentPlaceHolder1$ucJobSearchResult$criteria' => ''
        ];

        $urlRequest = $this->getListUrl($this->getSearch()) . http_build_query($params);

        $response = $this->client->request(
            'GET',
            $urlRequest
        );

        $crawler = new Crawler($response->getBody()->getContents());


        $nodeList = $crawler->filter("#ctl00_ContentPlaceHolder1_ucJobSearchResult_resultList table tr:not(:first-child)");

        $checkElements = [
            "first" => $nodeList
        ];

        $this->isModified($checkElements);

        $jobPostingArray = $nodeList->count() ? $nodeList->each(function (Crawler $node, $i) {
            $linkElement = $node->filter('td:first-child > a');
            $link = $linkElement->count() !== 0 ? 'http://besteam.com.hk/' . $linkElement->attr('href') : null;

            $jobPosting = $this->createJobPosting();
            return $jobPosting
                ->setUrl($link)
                ->setPublishedAt(new \DateTime())
                ->setJobLocation('hong kong')
                ->setHiringOrganization(new Organization("Besteam"))
                ->setTitle($linkElement ? $linkElement->text() : null);
        }) : [];

        $collection = new ArrayCollection($jobPostingArray);

        $paginationElement = $crawler->filter('#ctl00_ContentPlaceHolder1_ucJobSearchResult_btnNav_lblPageNum');

        $pagination = $paginationElement->count() ? $paginationElement->text() : null;

        $nbResults = null;
        if ($pagination) {
            preg_match('/Page \d+ of (\d+)/', $pagination, $matches);
            $nbResults = isset($matches[1]) ? $matches[1] : null;
        }

        $cursorNext = $cursor;
        if ($nbResults) {
            $cursorNext['nbResults'] = $nbResults;
        }
        $cursorNext['start'] = $cursorNext['start'] + 1;

        if (!$this->hasNextResult($cursorNext)) {
            $cursorNext = false;
        }

        if($cursorNext['start'] > 2){
            $cursorNext = false;
        }

        return new JobPostingResultSet($collection, $cursorNext);
    }

    public function hasNextResult($cursor)
    {
        return $cursor['start'] <= $cursor['nbResults'];
    }

    public function refine(JobPosting $jobPosting)
    {
        $response = $this->client->request(
            'GET',
            $jobPosting->getUrl()
        );

        $crawler = new Crawler($response->getBody()->getContents());

        $salaryElement = $crawler->filter('#ctl00_ContentPlaceHolder1_ucJobSearchResultDetails_lbSalary');
        $salary = $salaryElement ? $salaryElement->text() : null;

        $requirementsElement = $crawler->filter('#ctl00_ContentPlaceHolder1_ucJobSearchResultDetails_lbRequirement');
        $requirements = $requirementsElement ? $requirementsElement->text() : null;

        $contactElement = $crawler->filter('#ctl00_ContentPlaceHolder1_ucJobSearchResultDetails_lbContact');
        $tabContact = $contactElement ? $this->parseContact($contactElement->text()) : ['name' => null, 'email' => null];

        $contact = new Contact();
        $contact->setEmail($tabContact['email'])->setName($tabContact['name']);

        return $jobPosting
            ->setBaseSalary($salary)
            ->setSkills($requirements)
            ->setContact($contact);
    }

    public function parseContact($content)
    {
        $tabContact = [];
        preg_match('/(.*?)(\D+@\D+.\D+)/', $content, $matches);

        if(isset($matches[2])){
            $tabContact['email'] = $matches[2];
            $tabContact['name'] = $matches[1];
        }else if(isset($matches[1])){
            $tabContact['email'] = strpos($matches[1], '@') ? $matches[1] : null;
            $tabContact['name'] = null;
        }else{
            $tabContact['email'] = null;
            $tabContact['name'] = null;
        }
        return $tabContact;
    }

    public function getName()
    {
        return 'besteam';
    }

    private function getListUrl(Search $search)
    {
        return 'http://besteam.com.hk/jobSearchResult.aspx?txtKeyword=' . $search->getTerms() . '&ddlCategory=&ddlSalary=0&';
    }
}
