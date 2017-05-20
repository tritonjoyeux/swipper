<?php

namespace Fashiongroup\Swiper\Agents\Naukri;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Model\Contact;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class NaukriAgent extends AbstractAgent
{
    /**
     * @var Client
     */
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
            'nbResults' => null
        ];

        $cursor = array_merge($cursorDefault, $cursor);

        //params url
        $params = [
            "qp" => $this->search->getTerms(),
            "ql" => $this->search->getCountry(),
            "qe" => "",
            "qm" => "",
            "qx" => "",
            "qi[]" => "",
            "qf[]" => "",
            "qr[]" => "",
            "qs" => "f",
            "qo" => 2,
            "qjt[]" => "",
            "qk[]" => "",
            "qwdt" => "",
            "qsb_section" => "home",
            "qpremTagLabel" => "",
            "qwd[]" => "",
            "qcf[]" => "",
            "qci[]" => "",
            "qck[]" => "",
            "edu[]" => "",
            "qcug[]" => "",
            "qcpg[]" => "",
            "qctc[]" => "",
            "qco[]" => 2,
            "qcjt[]" => "",
            "qcr[]" => "",
            "qcl[]" => "",
            "qrefresh" => "",
            "xt" => "adv",
            "qtc[]" => "",
            "fpsubmiturl" => urlencode("https://www.naukri.com/" . sprintf(
                    '%s-jobs-in-%s',
                    $this->search->getTerms(),
                    $this->search->getCountry()
                )),
            "src" => "cluster",
            "px" => 1
        ];

        $urlRequest = 'https://www.naukri.com' .
            sprintf(
                '/%s-jobs-in-%s',
                strtolower($this->search->getTerms()),
                strtolower($this->search->getCountry())
            );

        //remove src&px for next pages and add pagination to url
        if ($cursor['start'] !== 1) {
            $urlRequest .= '-' . $cursor['start'];
            unset($params['src']);
            unset($params['px']);
        }

        $urlRequest .= '?'.http_build_query($params);

        $response = $this->client->request(
            'GET',
            $urlRequest
        );

        $crawler = new Crawler($response->getBody()->getContents());

        $nodeList = $crawler->filter(".srp_container.fl .content");

        $jobPostingArray = $nodeList->count() ? $nodeList->each(function (Crawler $node, $i) {
            if ($node->filter('.banner')->count() !== 1) {
                $titleElement = $node->filter('ul');
                $locationElement = $node->filter('span.loc');
                $organizationElement = $node->filter('span.org');

                $jobPosting = $this->createJobPosting()
                    ->setUrl($node->attr('href'))
                    ->setTitle($titleElement->count() !== 0 ? $titleElement->text() : null)
                    ->setJobLocation($locationElement->count() !== 0 ? $locationElement->text() : null);

                if ($organizationElement->count() !== 0) {
                    $jobPosting->setHiringOrganization(new Organization($organizationElement->text()));
                }
                return $jobPosting;
            }
            return null;
        }) : [];

        $paginationElement = $crawler->filter('.small_title .cnt');

        $paginationText = $paginationElement->count() ? $paginationElement->text() : null;

        //get nb result
        if ($paginationText) {
            $matches = [];
            preg_match('/\d+-\d+ of (\d+)/', $paginationText, $matches);
            $nbResults = (int)$matches[1];
        } else {
            $nbResults = $cursor["nbResults"];
        }

        $nextCursor = [
            "start" => $cursor["start"] + 1,
            "nbResults" => $nbResults
        ];

        $collection = new ArrayCollection($jobPostingArray);

        if (!$this->hasNextResultSet($nextCursor)) {
            $nextCursor = false;
        }

        return new JobPostingResultSet($collection, $nextCursor);
    }

    protected function hasNextResultSet($cursor)
    {
        return $cursor['nbResults'] >= ($cursor['start'] - 1) * 50;
    }

    public function refine(JobPosting $jobPosting)
    {
        $response = $this->client->request('GET', $jobPosting->getUrl());

        $crawler = new Crawler($response->getBody()->getContents());

        $descriptionElement = $crawler->filter('.listing.mt10');

        // get contact data
        $matches = [];
        preg_match('/-(\d+)\?/', $jobPosting->getUrl(), $matches);
        $jobId = $matches[1];

        $getContactDetailsUrl = sprintf('https://www.naukri.com/jd/contactDetails?file=%d', $jobId);

        $contactDetailsResponse = $this->client->request('GET', $getContactDetailsUrl);

        $contactDetails = \GuzzleHttp\json_decode($contactDetailsResponse->getBody()->getContents(), true);

        $contact = new Contact();

        $emailTitle = null;

        //several possible fields
        if (isset($contactDetails['fields']['Email Address']['title']) && filter_var($contactDetails['fields']['Email Address']['title'], FILTER_VALIDATE_EMAIL)) {
            $emailTitle = $contactDetails['fields']['Email Address']['title'];
        } elseif (isset($contactDetails['fields']['Recruiter Name']) && filter_var($contactDetails['fields']['Recruiter Name'], FILTER_VALIDATE_EMAIL)) {
            $emailTitle = $contactDetails['fields']['Recruiter Name'];
        }

        $contact->setEmail($emailTitle);

        return $jobPosting
            ->setDescription($descriptionElement ? $descriptionElement->text() : null)
            ->setContact($contact);
    }

    public function getName()
    {
        return 'naukri';
    }
}
