<?php

namespace Fashiongroup\Swiper\Agents\Indeed;

class DateParser
{
    private $pattern = '/(\d+)\+? (\w+) ago$/u';

    private $quantityTypeMap = array(
        'minute' => 'minute',
        'hour' => 'hour',
        'day' => 'day'
    );

    /**
     * @param $string
     * @return \DateTime
     */
    public function parse($string)
    {
        if(in_array($string, ['Just posted', 'Today', '最新发布', '新着'])) {
            return new \DateTime();
        }

        preg_match($this->pattern, $string, $matches);

        if (count($matches) !== 3) {
            return null;
        }

        list(, $amout, $type) = $matches;

        // get singular form
        $quantityType = rtrim($type, 's');

        $quantityType = $this->parseQuantityType($quantityType);

        if (!$quantityType) {
            return null;
        }

        try {
            return new \DateTime(sprintf('now - %d %s', $amout, $quantityType));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param string $pattern
     * @return $this
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * @param array|null $quantityTypeMap
     * @return $this
     */
    public function setQuantityTypeMap($quantityTypeMap)
    {
        $this->quantityTypeMap = $quantityTypeMap;

        return $this;
    }

    protected function parseQuantityType($string)
    {
        return isset($this->quantityTypeMap[$string]) ? $this->quantityTypeMap[$string] : null;
    }
}
