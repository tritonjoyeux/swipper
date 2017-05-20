<?php

namespace Fashiongroup\Swiper;

class Search
{
    /**
     * @var string
     */
    private $terms;

    /**
     * @var \DateTime
     */
    private $since;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string Country code
     */
    private $country;

    /**
     * @var string Agent name
     */
    private $agent;

    /**
     * @var array
     */
    private $extras;

    /**
     * @var int
     */
    private $freshness;

    /**
     * Search constructor.
     */
    public function __construct($terms)
    {
        $this->terms = $terms;
    }

    /**
     * @param \DateTime $since
     * @return $this
     */
    public function setSince(\DateTime $since)
    {
        $this->since = $since;

        return $this;
    }

    /**
     * @param $location
     * @return $this
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param mixed $agent
     * @return Search
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
        return $this;
    }

    /**
     * @return array
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * @param array $extras
     * @return Search
     */
    public function setExtras($extras)
    {
        $this->extras = $extras;
        return $this;
    }

    public function getExtra($key, $default = null)
    {
        return isset($this->extras[$key]) ? $this->extras[$key] : $default;
    }

    /**
     * @return mixed
     */
    public function getFreshness()
    {
        return $this->freshness;
    }

    /**
     * @param mixed $freshness
     * @return Search
     */
    public function setFreshness($freshness)
    {
        $this->freshness = $freshness;
        return $this;
    }
}
