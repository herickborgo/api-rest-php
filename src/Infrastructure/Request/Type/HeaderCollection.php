<?php

namespace HerickBorgo\RestApi\Infrastructure\Request\Type;

class HeaderCollection
{
    /** @var array */
    public $value = [];

    public function __construct(array $value = [])
    {
        $this->value = $value;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key = '', $default = null)
    {
        if (!$key) {
            return $this->value;
        }
        if (key_exists($key, $this->value)) {
            return $this->value[$key];
        }
        return $default;
    }
}
