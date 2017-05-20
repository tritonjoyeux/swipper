<?php

namespace Fashiongroup\Swiper\Agents\Computrabajo;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;
use Fashiongroup\Swiper\Search;

class ComputrabajoAgent extends AbstractAgent
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

        $cursorDefault = [
            'start' => 1,
            'nbResults' => null
        ];

        $cursor = array_merge($cursorDefault, $cursor);

        $this->session->visit($this->getListUrl($this->search, $cursor));

        $page = $this->session->getPage();

        $rows = $page->findAll('css', '.bRS.bClick');

        foreach ($rows as $row) {
            $link = $row->find('css', '.iO > .tO > a');
            $url = $link ? $this->session->getLinkUrl($link) : null;

            $datePostingElement = $row->find('css', '.dO');
            $datePosting = $datePostingElement ? $datePostingElement->getText() : null;

            if ($datePosting) {
                if (strpos($datePosting, 'Hoy') !== false) {
                    $matches = [];
                    preg_match('/Hoy, ([^:]*):([^ ]*) ([^.]*).*([^.]*)/', $datePosting, $matches);
                    if ($matches[3] . $matches[4] = 'pm') {
                        $matches[2] += 12;
                    }
                    $datePosting = date_create_from_format('d/m/Y H:i', date('d/m/Y') . ' ' . $matches[1] . ':' . $matches[2]);
                } elseif (strpos($datePosting, 'Ayer') !== false) {
                    $matches = [];
                    preg_match('/Ayer, ([^:]*):([^ ]*) ([^.]*).*([^.]*)/', $datePosting, $matches);
                    if ($matches[3] . $matches[4] = 'pm') {
                        $matches[2] += 12;
                    }
                    $datePosting = date_create_from_format('d/m/Y H:i', date("d/m/Y", time() - 60 * 60 * 24) . ' ' . $matches[1] . ':' . $matches[2]);
                } else {
                    $matches = [];
                    preg_match('/(\d+) (\w+)/', $datePosting, $matches);
                    $mounth = $this->getMounth(strtolower($matches[2]));
                    $day = $matches[1];

                    $datePosting = date_create_from_format('d/m/Y H:i', $day . '/' . $mounth . '/' . date('Y').' 00:00');
                }
            }

            $hiringOrganizationElements = $row->find('css', '.w_100.fl.mtb5.lT > span:first-child');
            $hiringOrganization = $hiringOrganizationElements ? new Organization($hiringOrganizationElements->getText()) : null;

            $jobLocationElements = $row->find('css', '.w_100.fl.mtb5.lT > span:nth-child(2)');
            $jobLocation = $jobLocationElements ? $jobLocationElements->getText() : null;

            $jobLinkElement = $row->find('css', '.js-o-link');
            $jobLink = $jobLinkElement ? $jobLinkElement->getText() : null;

            $jobPosting = $this->createJobPosting()
                ->setUrl($url)
                ->setPublishedAt($datePosting)
                ->setJobLocation($jobLocation)
                ->setHiringOrganization($hiringOrganization)
                ->setTitle($jobLink);

            $collection->add($jobPosting);
        }

        $nbResultsElement = $page->find('css', '.breadtitle_mvl > h1 > span');
        $nbResults = $nbResultsElement ? $nbResultsElement->getText() : null;

        $nextCursor = [
            "start" => $cursor["start"] + 1,
            "nbResults" => $nbResults
        ];

        if (!$this->hasNextResultSet($nextCursor)) {
            $nextCursor = false;
        }

        return new JobPostingResultSet($collection, $nextCursor);
    }

    protected function hasNextResultSet($cursor)
    {
        return $cursor['nbResults'] >= $cursor['start'] * 20;
    }

    public function refine(JobPosting $jobPosting)
    {
        $this->session->visit($jobPosting->getUrl());
        $page = $this->session->getPage();

        $descriptionElement = $page->find('css', '.cm-12.box_i ul li:nth-child(7)');

        $description = $descriptionElement ? $descriptionElement->getText() : null;

        return $jobPosting->setDescription($description);
    }

    public function getName()
    {
        return 'computrabajo';
    }

    protected function getListUrl(Search $search, $cursor)
    {
        $tabHosts = [
            "colombie" => 'http://www.computrabajo.com.co/ofertas-de-trabajo/',
            "mexique" => 'http://www.computrabajo.com.mx/ofertas-de-trabajo/',
            "perou" => 'http://www.computrabajo.com.pe/ofertas-de-trabajo/',
            "argentine" => 'http://www.computrabajo.com.ar/ofertas-de-trabajo/',
            "chili" => 'http://www.computrabajo.cl/ofertas-de-trabajo/',
            "venezuela" => 'http://www.computrabajo.com.ve/ofertas-de-trabajo/',
            "costa rica" => 'http://www.computrabajo.co.cr/ofertas-de-trabajo/',
            "uruguay" => 'www.uy.computrabajo.com/ofertas-de-trabajo/',
            "paraguay" => 'http://www.py.computrabajo.com/ofertas-de-trabajo/',
            "bolivie" => 'http://www.bo.computrabajo.com/ofertas-de-trabajo/',
        ];

        if (isset($tabHosts[$search->getCountry()])) {
            $params = [
                "p" => $cursor['start'],
                "q" => $search->getTerms()
            ];

            $url = $tabHosts[$search->getCountry()];

            $url .= "?" . http_build_query($params);
            return $url;
        }
        exit(dump("pays incorrect"));
    }

    protected function getMounth($mounth)
    {
        $tabMounth = [
            "enero" => "1",
            "febrero" => "2",
            "marzo" => "3",
            "abril" => "4",
            "mayo" => "5",
            "junio" => "6",
            "julio" => "7",
            "agosto" => "8",
            "septiembre" => "9",
            "setiembre" => "9",
            "octubre" => "10",
            "noviembre" => "11",
            "diciembre" => "12",
        ];

        if (isset($tabMounth[$mounth])) {
            return $tabMounth[$mounth];
        }

        return null;
    }
}
