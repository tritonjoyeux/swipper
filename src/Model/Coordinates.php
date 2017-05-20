<?php

namespace Fashiongroup\Swiper\Model;

class Coordinates
{
    /**
     * @var double
     */
    private $latitude;

    /**
     * @var double
     */
    private $longitude;

    /**
     * Returns the latitude.
     *
     * @return double|null
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Returns the longitude.
     *
     * @return double|null
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $latitude
     * @return Coordinates
     */
    public function setLatitude($latitude)
    {
        $latitude = (double)$latitude;
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * @param float $longitude
     * @return Coordinates
     */
    public function setLongitude($longitude)
    {
        $longitude = (double)$longitude;
        $this->longitude = $longitude;
        return $this;
    }
}
