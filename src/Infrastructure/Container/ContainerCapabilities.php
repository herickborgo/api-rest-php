<?php

namespace HerickBorgo\RestApi\Infrastructure\Container;

/**
 * @codeCoverageIgnore
 */
trait ContainerCapabilities
{
    /**
     * @var Container
     */
    private $container;

    /**
     * ContainerCapabilities constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param Container $container
     * @return static
     */
    public static function with(Container $container)
    {
        return new static($container);
    }

    /**
     * @return static
     */
    public static function withContainer()
    {
        return Container::instance()->lazy(self::class, [Container::instance()]);
    }
}
