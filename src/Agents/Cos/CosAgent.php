<?php

namespace Fashiongroup\Swiper\Agents\Cos;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\FormatterLibrary\Formatter;
use Fashiongroup\Swiper\Model\EmploymentTypeEnum;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use Fashiongroup\Swiper\Search;

class CosAgent extends AbstractAgent
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
        $url = $this->getListUrl($this->search);

        $this->session->visit($url);

        $page = $this->session->getPage();

        $rows = $page->findAll('css', 'tr.joblist'); // toute les offres

        $checkElements = [
            "first" => $page->find('css', '.shoplink'),
            "second" => $page->find('css', '.navigation'),
            "third" => $page->find('css', '.logotype')
        ];

        $this->isModified($checkElements);

        $collection = new ArrayCollection();

        foreach ($rows as $row) {
            $jobPosting = $this->createJobPosting();

            $jobLink = $row->find('css', '.col1 a'); // lien de l'offre
            $jobEmployementType = $row->find('css', '.col4')->getText(); // plein temps ou temps partiel

            $url = $this->session->getLinkUrl($jobLink); //url du lien

            // extract date from url
            $matches = [];
            preg_match('/(\d+)\/(\d+)\/(\d+)\/\d+\.html$/', $url, $matches);

            $publishedAt = new \DateTime(sprintf('%d-%d-%d', $matches[1], $matches[2], $matches[3]));

            $jobEmployementType = $jobEmployementType == 'Full-time' ? EmploymentTypeEnum::FULL_TIME : EmploymentTypeEnum::PART_TIME;
            $jobPosting
                ->setTitle($jobLink->getText())
                ->setUrl($url)
                ->setPublishedAt($publishedAt)
                ->setJobLocation($row->find('css', '.col3')->getText() . ' - ' . $row->find('css', '.col2')->getText())
                ->setEmploymentType($jobEmployementType)
                ->setHiringOrganization(new Organization('Cos'));

            $collection->add($jobPosting);
        }

        return new JobPostingResultSet($collection, false);
    }

    public function refine(JobPosting $jobPosting)
    {
        $formatter = new Formatter();
        $this->session->visit($jobPosting->getUrl());
        $pageAnnonce = $this->session->getPage();

        $rows = $pageAnnonce->findAll('css', 'div.text.parbase.section > div > div'); //tous les child sauf le 1e

        if (count($rows) == 0) {
            $rows = $pageAnnonce->findAll('css', 'div.text.parbase.section div *:not(:first-child)'); //tous les child sauf le 1e
        }

        $description = [];

        foreach ($rows as $row) {
            $description[] = $row->getText();
        }

        $description = strip_tags(implode("\r", $description));

        preg_match('/(Who are we looking for.*)/', $description, $skills);
        $skills = isset($skills[1]) ? $skills[1] : null;

        if (!$skills) {
            preg_match('/(Job Requirements.*)/', $description, $skills);
            $skills = isset($skills[1]) ? $skills[1] : null;
            $description = $formatter->replaceByVoidUntilEnd($description, 'Job Requirements');
        } else {
            $description = $formatter->replaceByVoidUntilEnd($description, 'About COS');
        }

        if ($this->getSearch()->getCountry() == 'hk') {
            $jobPosting->setComplement($jobPosting->getJobLocation());
            $jobPosting->setJobLocation('Hong Kong city');
        }

        $description = strip_tags($description);
        $description = $formatter->reduceLineBreak($description);
        $description = $formatter->replaceEndAndStartLineBreak($description);
        return $jobPosting
            ->setDescription($description)
            ->setSkills($skills);
    }

    public function getName()
    {
        return 'cos';
    }

    protected function getListUrl(Search $search)
    {
        return 'http://career.cosstores.com/coscareer/en/findjob.html?' . http_build_query([
                'country' => 'coscareer:location/' . $search->getCountry()
            ]);
    }
}
