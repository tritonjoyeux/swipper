<?php

namespace Fashiongroup\Swiper\Agents\CareerNext;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\FormatterLibrary\Formatter;
use Fashiongroup\Swiper\Model\ContractTypeEnum;
use Fashiongroup\Swiper\Model\EmploymentTypeEnum;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use GuzzleHttp\Client;

class CareerNextAgent extends AbstractAgent
{
    /**
     * @var Client
     */
    private $client;

    /**
     * CareerNextAgent constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function search($cursor = [])
    {
        $cursorDefault = [
            'page' => 1
        ];

        $cursor = array_merge($cursorDefault, $cursor);

        $collection = new ArrayCollection();

        $response = $this->client->request(
            'POST',
            '/Umbraco/Screenmedia/VacancySearch/Search',
            [
                'json' => [
                    'SearchQuery' => $this->search->getTerms(),
                    'CurrentPage' => $cursor['page'],
                    'FindAStore' => false,
                    'HoursMax' => '44:00',
                    'HoursMin' => "06:00",
                ]
            ]
        );

        $json = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);

        $checkElements = [
            "first" => isset($json['ResultsSize']),
            "second" => isset($json['RequestQuery'])
        ];

        $this->isModified($checkElements);

        foreach ($json['Results'] as $result) {
            $jobPosting = $this->createJobPosting();

            $date = new \DateTime($result['PublishedDate']);

            $employementType = ($result['HoursPerWeek'] > 34) ? EmploymentTypeEnum::FULL_TIME : EmploymentTypeEnum::PART_TIME;

            $contractType = (false !== stripos($result['Title'], 'temporary')) ? ContractTypeEnum::FIXED_TERM_CONTRACT : ContractTypeEnum::ROLLING_CONTRACT;

            $jobPosting
                ->setTitle($result['Title'])
                ->setEmploymentType($employementType)
                ->setContractType($contractType)
                ->setUrl('https://careers.next.co.uk/vacancies#/Item?id=' . $result['ID'])
                ->setBaseSalary($result['MinPotentialEarning'])
                ->setHiringOrganization(new Organization('Next'))
                ->setPublishedAt($date);

            // get job location
            foreach (['Address', "TownCity", "Location"] as $key) {
                if (!empty($result[$key])) {
                    $jobPosting->setJobLocation($result[$key] . ' - UK');
                    break;
                }
            }

            if (!$jobPosting->getJobLocation()) {
                continue;
            }

            $collection->add($jobPosting);
        }

        $nextCursor = [
            'page' => $cursor['page'] + 1,
            'ResultsSize' => $json['ResultsSize']
        ];

        if ($this->hasNextResultSet($nextCursor)) {
            $nextCursor = false;
        }

        return new JobPostingResultSet($collection, $nextCursor);
    }

    /**
     * @param $cursor
     * @return bool
     */
    protected function hasNextResultSet($cursor)
    {
        return ($cursor['page'] - 1) * 10 >= $cursor['ResultsSize'];
    }

    public function refine(JobPosting $jobPosting)
    {
        $formatter = new Formatter();

        $matches = [];

        preg_match('/id\=(\d+)$/', $jobPosting->getUrl(), $matches);

        $id = (int)$matches[1];

        $response = $this->client->request(
            'POST',
            '/Umbraco/Screenmedia/VacancySearch/JobById',
            [
                'json' => [
                    'ID' => $id
                ],
                'connect_timeout' => 5
            ]
        );

        $json = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
        $skills = $json['Result']['SkillRequirements'] . "\r" . $json['Result']['Benefits'];
        $description = $json['Result']['JobDescription'];

        $description = $formatter->replaceBy($description, '&.{1,5};', '');
        //$description = preg_replace('/&.*?;/', '', $description);

        $description = $formatter->replaceByVoid($description, '<strong>About the Role<\/strong><br\/> <br\/> <strong>You will:<\/strong><br\/> <br\/>');
        //$description = preg_replace('/<strong>About the Role<\/strong><br\/> <br\/> <strong>You will:<\/strong><br\/> <br\/>/', '', $description);

        $description = $formatter->replaceByVoid($description, '<strong>About the team<br\/><\/strong>');
        //$description = preg_replace('/<strong>About the team<br\/><\/strong>/', '', $description);

        $description = $formatter->replaceBy($description, '<br.*?\/>', "\r\n");

        $skills = $formatter->replaceBy($skills, '&.{1,5};', '');
        //$skills = preg_replace('/&.*?;/', '', $skills);

        $skills = $formatter->replaceByVoid($skills, '<strong>About You<\/strong><br\/> <br\/>');
        //$skills = preg_replace('/<strong>About You<\/strong><br\/> <br\/>/', '', $skills);

        return $jobPosting
            ->setDescription($description)
            ->setSkills($skills);
    }

    public function getName()
    {
        return 'career_next';
    }
}
