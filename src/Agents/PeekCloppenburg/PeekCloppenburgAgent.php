<?php

namespace Fashiongroup\Swiper\Agents\PeekCloppenburg;

use Doctrine\Common\Collections\ArrayCollection;
use Fashiongroup\Swiper\Agents\AbstractAgent;
use Fashiongroup\Swiper\Agents\JobPostingResultSet;
use Fashiongroup\Swiper\Agents\Session;
use Fashiongroup\Swiper\FormatterLibrary\Formatter;
use Fashiongroup\Swiper\Model\JobPosting;
use Fashiongroup\Swiper\Model\Organization;

class PeekCloppenburgAgent extends AbstractAgent
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
            'start' => 1
        ];

        $cursor = array_merge($cursorDefault, $cursor);

        $this->session->visit($this->getListUrl($cursor));
        $page = $this->session->getPage();

        if ($this->session->getCurrentUrl() == 'https://recruitment.peek-cloppenburg.com/at/jobposting/4') {
            $button = $page->find('css', '.dark');
            $button->click();
        }

        $checkElements = [
            "first" => $page->find('css', '.container'),
            "second" => $page->find('css', '#disclaimer'),
            "third" => $page->find('css', '.menu_switch')
        ];

        $this->isModified($checkElements);

        $nodeElements = $page->findAll('css', '.job');

        foreach ($nodeElements as $element) {
            $linkElement = $element->find('css', 'a');
            $link = $linkElement ? $this->session->getLinkUrl($linkElement) : null;

            $titleElement = $element->find('css', '.position');
            $title = $titleElement ? $titleElement->getText() : null;

            $locationElement = $element->find('css', '.location');
            $location = $locationElement ? $locationElement->getText() : null;

            $countryElement = $element->find('css', '.country');
            $country = $countryElement ? $countryElement->getText() : null;

            $organization = new Organization("Peek & Cloppenburg");

            $jobPosting = $this->createJobPosting();

            if (strtoupper($country) == "DEUTSCHLAND")
                $collection->add(
                    $jobPosting->setTitle($title)
                        ->setJobLocation($location)
                        ->setUrl($link)
                        ->setHiringOrganization($organization)
                        ->setPublishedAt(new \DateTime())
                );
        }

        $arrowNextElement = $page->find('css', '.arrow_next');
        if ($arrowNextElement) {
            $cursor = [
                "start" => $cursor["start"] + 1
            ];
        } else {
            $cursor = false;
        }

        return new JobPostingResultSet($collection, $cursor);
    }

    public function refine(JobPosting $jobPosting)
    {

        $this->session->visit($jobPosting->getUrl());

        $page = $this->session->getPage();

        $annonceElement = $page->find('css', '.DEFAULT');

        $description = $annonceElement ? $this->parseAnnonceElement($annonceElement->getOuterHtml()) : null;

        $skills = $annonceElement ? $this->getSkills($annonceElement->getOuterHtml()) : null;

        return $jobPosting
            ->setDescription($description)
            ->setSkills($skills);
    }

    private function getSkills($element)
    {
        $formatter = new Formatter();
        preg_match('/WAS SIE MITBRINGEN(.*?)WAS WIR BIETEN.*?HABEN WIR IHR INTERESSE GEWECKT\?(.*)/s', $element, $matches);
        $skills = isset($matches[1]) ? $matches[1] : null;
        $skills .= isset($matches[2]) ? "\n" . $matches[2] : null;
        $skills = $formatter->reduceLineBreak(strip_tags($skills));
        return strip_tags($skills);
    }

    private function parseAnnonceElement($element)
    {
        $formatter = new Formatter();
        preg_match('/WAS SIE ERWARTET(.*?)WAS SIE MITBRINGEN.*?WAS WIR BIETEN(.*?)HABEN WIR IHR INTERESSE GEWECKT/s', $element, $matches);
        $description = isset($matches[1]) ? $matches[1] : null;
        $description .= isset($matches[2]) ? "\n" . $matches[2] : null;
        $description = $formatter->reduceLineBreak(strip_tags($description));
        return $description;
    }

    public function getName()
    {
        return 'peek_cloppenburg';
    }

    protected function getListUrl($cursor)
    {
        return 'https://recruitment.peek-cloppenburg.com/at/jobposting/search/0/' . $cursor["start"];
    }
}
