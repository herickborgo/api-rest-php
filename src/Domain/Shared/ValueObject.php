<?php

namespace HerickBorgo\RestApi\Domain\Shared;

class ValueObject
{
    private $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
