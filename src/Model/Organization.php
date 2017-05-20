<?php
/**
 * Created by PhpStorm.
 * User: pocos
 * Date: 23/02/17
 * Time: 16:27
 */

namespace Fashiongroup\Swiper\Model;

class Organization
{
    private $name;

    private $description;

    /**
     * Organization constructor.
     * @param $name
     */
    public function __construct($name = null)
    {
        $this->name = $name;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Organization
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return Organization
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function toArray()
    {
        return [
            "name" => $this->name,
            "description" => $this->description
        ];
    }
}
