<?php

namespace HerickBorgo\RestApi\Domain\Vo;

use HerickBorgo\RestApi\Domain\Shared\ValueObject;
use InvalidArgumentException;

class Str extends ValueObject
{
    public function __construct(string $value = null)
    {
        if (!$value instanceof String) {
            throw new InvalidArgumentException('Invalid Value');
        }
        parent::__construct($value);
    }
}
