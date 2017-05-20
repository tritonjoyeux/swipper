<?php

namespace Fashiongroup\Swiper\Model;

class Address
{
    /**
     * @var Coordinates|null
     */
    private $coordinates;

    /**
     * @var string|int|null
     */
    private $streetNumber;

    /**
     * @var string|null
     */
    private $streetName;

    /**
     * @var string|null
     */
    private $subLocality;

    /**
     * @var string|null
     */
    private $locality;

    /**
     * @var string|null
     */
    private $postalCode;

    /**
     * @var array
     */
    private $adminLevels;

    /**
     * @var Country|null
     */
    private $country;

    /**
     * {@inheritDoc}
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * {@inheritDoc}
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * {@inheritDoc}
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * {@inheritDoc}
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * {@inheritDoc}
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubLocality()
    {
        return $this->subLocality;
    }

    /**
     * {@inheritDoc}
     */
    public function getAdminLevels()
    {
        return $this->adminLevels;
    }

    /**
     * {@inheritDoc}
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        $lat = null;
        $lon = null;
        if (null !== $coordinates = $this->getCoordinates()) {
            $lat = $coordinates->getLatitude();
            $lon = $coordinates->getLongitude();
        }
        $countryName = null;
        $countryCode = null;
        if (null !== $country = $this->getCountry()) {
            $countryName = $country->getName();
            $countryCode = $country->getCode();
        }
        return array(
            'latitude' => $lat,
            'longitude' => $lon,
            'streetNumber' => $this->streetNumber,
            'streetName' => $this->streetName,
            'postalCode' => $this->postalCode,
            'locality' => $this->locality,
            'subLocality' => $this->subLocality,
            'adminLevels' => $this->adminLevels,
            'country' => $countryName,
            'countryCode' => $countryCode,
        );
    }

    public function __toString()
    {
        return join(' ', [
            $this->streetNumber,
            $this->streetName,
            $this->postalCode,
            $this->locality,
            $this->subLocality,
            join(' ', $this->adminLevels),
            $this->country->getName()
        ]);
    }

    /**
     * @param Coordinates|null $coordinates
     * @return Address
     */
    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;
        return $this;
    }

    /**
     * @param int|null|string $streetNumber
     * @return Address
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;
        return $this;
    }

    /**
     * @param null|string $streetName
     * @return Address
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;
        return $this;
    }

    /**
     * @param null|string $subLocality
     * @return Address
     */
    public function setSubLocality($subLocality)
    {
        $this->subLocality = $subLocality;
        return $this;
    }

    /**
     * @param null|string $locality
     * @return Address
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
        return $this;
    }

    /**
     * @param null|string $postalCode
     * @return Address
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @param array $adminLevels
     * @return Address
     */
    public function setAdminLevels($adminLevels)
    {
        $this->adminLevels = $adminLevels;
        return $this;
    }

    /**
     * @param Country|null $country
     * @return Address
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }
}
