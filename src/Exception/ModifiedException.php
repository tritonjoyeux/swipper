<?php

namespace Fashiongroup\Swiper\Exception;


class ModifiedException extends \Exception
{
    public function __construct($message, $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}