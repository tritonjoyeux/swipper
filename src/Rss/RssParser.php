<?php

namespace Fashiongroup\Swiper\Rss;

use Fashiongroup\Swiper\Agents\Session;

class RssParser
{
    private $session;
    private $page;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function setUrlXml($url)
    {
        $this->session->visit($url);
        $this->page = $this->session->getPage();
        return $this;
    }

    public function fetch()
    {
        $content = $this->page->getContent();

        $content = str_replace('content:', '', $content);

        return simplexml_load_string($content);
    }
}
