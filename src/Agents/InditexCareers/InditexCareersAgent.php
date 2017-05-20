<?php

namespace Fashiongroup\Swiper\Agents\InditexCareers;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\FormatterLibrary\Formatter;
use Fashiongroup\Swiper\Model\Address;
use Fashiongroup\Swiper\Model\Contact;
use Fashiongroup\Swiper\Model\EmploymentTypeEnum;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Psr7\Request;

class InditexCareersAgent extends AbstractAgent
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var Session
     */
    private $session;

    /**
     * @var CookieJar
     */
    private $cookieJar;

    /**
     * @var string auth token
     */
    private $auth;

    /**
     * CareerNextAgent constructor.
     * @param Client $client
     */
    public function __construct(Client $client, Session $session)
    {
        $this->client = $client;
        $this->session = $session;
        $this->cookieJar = new CookieJar();
    }

    public function search($cursor = [])
    {
        $collection = new ArrayCollection();

        $cursorDefault = [
            'start' => 0,
            'nbResults' => 0
        ];
        $cursor = array_merge($cursorDefault, $cursor);

        $brands = $this->getSearch()->getExtra("brands");

        $data = [
            'country' => $this->getCountryId($this->getSearch()->getCountry()),
            'offset' => $cursor['start'],
            'p_auth' => $this->getAuth(),
            'p_p_id' => 'busquedaofertasportlet_WAR_appwebportaljoinfashionportlet',
            'p_p_lifecycle' => 1,
            'p_p_state' => 'normal',
            'p_p_mode' => 'view',
            'p_p_col_id' => 'column-3',
            'p_p_col_count' => 1,
            '_busquedaofertasportlet_WAR_appwebportaljoinfashionportlet_javax.portlet.action' => 'searchOffers'
        ];

        $query = http_build_query($data);
        if ($brands !== null) {
            foreach ($brands as $brand) {
                $query .= '&brand=' . $this->getBrandId($brand);
            }
        }

        $request = new Request(
            'GET',
            '/portalweb/en/openings-employment?' . $query
        );

        $response = $this->client->send(
            $request,
            [
                'cookies' => $this->cookieJar
            ]
        );

        $result = $response->getBody()->getContents();
        $json = \GuzzleHttp\json_decode($result, true);

        $checkElements = [
            "first" => isset($json['countryIds']),
            "second" => isset($json['offers'])
        ];

        $this->isModified($checkElements);

        foreach ($json['offers'] as $offer) {
            $jobPosting = $this->createJobPosting();
            $jobPosting
                ->setUrl('https://www.inditexcareers.com/portalweb/en/offer' . $offer['urlDetalle'])
                ->setJobLocation($offer['countryName'] . ' - ' . $offer['cityName'])
                ->setTitle($offer['title'])
                ->setHiringOrganization(new Organization($offer['brandName']))
                ->setPublishedAt(new \DateTime());
            if (strpos(str_replace(' ', '', strtolower($jobPosting->getTitle())), 'fulltime')) {
                $jobPosting->setEmploymentType(EmploymentTypeEnum::FULL_TIME);
            }

            if (strpos(str_replace(' ', '', strtolower($jobPosting->getTitle())), 'parttime')) {
                $jobPosting->setEmploymentType(EmploymentTypeEnum::PART_TIME);
            }
            $contact = new Contact();

            $collection->add($jobPosting->setContact($contact->setEmail("candidaturas@pt.inditex.com")));
        }

        $nextCursor = [
            'start' => $json['nextOffset'],
            'nbResults' => $cursor['nbResults'] + count($json['offers'])
        ];

        if (!$this->hasNextResultSet($json)) {
            $nextCursor = false;
        }
        return new JobPostingResultSet($collection, $nextCursor);
    }

    /**
     * @return string
     */
    private function getAuth()
    {
        if (!$this->auth) {
            $response = $this->client->request(
                'POST',
                '/portalweb/en/openings-employment?nuevaBusqueda=1&country=542859',
                [
                    'cookies' => $this->cookieJar
                ]
            );
            preg_match('/https:\/\/www\.inditexcareers\.com\/portalweb\/en\/openings-employment\?p\_auth\=(\w+)/', $response->getBody()->getContents(), $matches);
            $this->auth = $matches[1];
        }
        return $this->auth;
    }

    /**
     * @param $json
     * @return bool
     */
    protected function hasNextResultSet($json)
    {
        return count($json['offers']) == 10;
    }

    public function refine(JobPosting $jobPosting)
    {
        $formatter = new Formatter();

        $this->session->visit($jobPosting->getUrl());
        $page = $this->session->getPage();

        $descriptionElements = $page->find('css', '.block-text.block-first');
        if ($descriptionElements) {
            $terms = [
                '\\t' => "",
                '[\w-]+@([\w-]+\.)+[\w-]+' => ""
            ];
            $description = $formatter->multipleReplaceBy($descriptionElements->getOuterHtml(), $terms);
            $description = $formatter->reduceLineBreak(strip_tags(preg_replace("/<li.*?>/", "- ", $description)));
            $description = str_replace("\r\n", "", $description);
            $description = str_replace("Stuur nu je sollicitatie naar", "", $description);
            $description = str_replace("Wil jij werken voor INDITEX? Wij zijn de grootste retail organisatie van de wereld en blijven nog steeds groeien! Stuur direct je CV met motivatie naar of via   www.inditexcareers.com", "", $description);
        } else {
            $description = null;
        }

        return $jobPosting
            ->setDescription($description);
    }

    public function getName()
    {
        return 'inditex_careers';
    }

    public function getCountryId($countryName)
    {
        $country = [
            "netherland" => 542859,
            "hk" => 541767,
            "australia" => 541347,
            "india" => 542061,
            "japan" => 542943,
            "china" => 542985,
            "south korea" => 540633,
            "taiwan" => 543027,
            "portugal" => 542229
        ];

        if (isset($country[$countryName])) {
            return $country[$countryName];
        }

        throw new \Exception("Unexcepted country");
    }


    public function getBrandId($brandName)
    {
        $brands = [
            "zara" => 539059,
            "inditex" => 539123,
            "oysho" => 539105,
            "zara home" => 539115,
            "bershka" => 553311,
            "pull and bear" => 539042,
            "massimo dutti" => 540948,
            "uterqüe" => 539021,
            "stradivarius" => 539110
        ];

        if (isset($brands[$brandName])) {
            return $brands[$brandName];
        }

        throw new \Exception("Unexcepted brand");
    }
}
