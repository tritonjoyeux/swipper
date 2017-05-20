<?php

namespace Fashiongroup\Swiper\Agents;

use Behat\Mink\Element\NodeElement;

interface Session
{
    /**
     * @return mixed
     */
    public function getPage();

    /**
     * @param $url
     * @return void
     */
    public function visit($url);

    /**
     * @return mixed
     */
    public function getCurrentUrl();

    public function getLinkUrl(NodeElement $nodeElement);

    public function getAbsoluteUrl($uri);
}
