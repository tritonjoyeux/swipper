<?php

namespace Fashiongroup\Swiper\Agents;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session as MinkSession;

class MinkSessionAdapter implements Session
{
    /**
     * MinkSession
     */
    private $minkSession;

    /**
     * MinkSessionAdapter constructor.
     */
    public function __construct(MinkSession $minkSession)
    {
        $this->minkSession = $minkSession;
    }

    public function getPage()
    {
        return $this->minkSession->getPage();
    }

    public function visit($url)
    {
        $this->minkSession->visit($url);
    }

    public function getCurrentUrl()
    {
        return $this->minkSession->getCurrentUrl();
    }

    public function getLinkUrl(NodeElement $nodeElement)
    {
        return $this->getAbsoluteUrl($nodeElement->getAttribute('href'));
    }

    public function getAbsoluteUrl($uri)
    {
        $parsedUrl = parse_url($this->minkSession->getCurrentUrl());

        return sprintf('%s://%s%s', $parsedUrl['scheme'], $parsedUrl['host'], $uri);
    }
}
