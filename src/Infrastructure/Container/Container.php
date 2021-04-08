<?php

namespace HerickBorgo\RestApi\Infrastructure\Container;

use Closure;
use Traversable;

/**
 * @codeCoverageIgnore
 * Container de dependências para uso global
 */
class Container implements \IteratorAggregate, \ArrayAccess
{
    /**
     * @var Container
     */
    private static $instance;


    /**
     * @var array
     */
    private $values = [];


    /**
     * @var array
     */
    private $closures = [];



    /**
     * @return Container
     */
    public static function instance(): Container
    {
        if (!self::$instance) {
            self::$instance = new static();
            self::$instance->addDefaultDependencies();
        }

        return self::$instance;
    }


    /**
     * @return Container
     */
    public static function clearInstance(): Container
    {
        self::$instance = null;
        return self::instance();
    }


    /**
     * @return void
     */
    public function addDefaultDependencies(): void
    {
        $this->addDependencies(require __DIR__.'/../../dependencies.php');
    }


    /**
     * @param string $id
     * @param array|null $params
     * @return mixed
     */
    public static function lazyLoad(string $id, array $params = null): mixed
    {
        return self::instance()->lazy($id, $params);
    }


    /**
     * @param string $id
     * @return mixed
     */
    public static function retrieve(string $id): mixed
    {
        return self::instance()->get($id);
    }


    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $id = (string)$key;
        return isset($this->values[$id]) || isset($this->closures[$id]);
    }


    /**
     * @param string $key
     * @param bool   $cached
     * @return mixed|null
     */
    public function get(string $key, $cached = true): ?mixed
    {
        if (!$this->has($key)) {
            return null;
        }

        return $cached ? $this->retrieveCached($key) : $this->retrieveNotCached($key);
    }


    /**
     * @param string $key
     * @return mixed
     */
    private function retrieveNotCached(string $key): mixed
    {
        return isset($this->closures[$key]) ? $this->closures[$key]() : $this->values[$key];
    }


    /**
     * @param string $key
     * @return mixed
     */
    private function retrieveCached(string $key): mixed
    {
        $value = isset($this->values[$key]) ? $this->values[$key] : $this->closures[$key]();
        $this->values[$key] = $value;
        return $this->values[$key];
    }


    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function set(string $key, $value): void
    {
        if ($value instanceof \Closure) {
            $this->closures[$key] = $value;
            return;
        }

        $this->values[$key] = $value;
    }


    /**
     * @param string $id
     * @param array|null $parameters
     * @return mixed|null
     */
    public function lazy(string $id, array $parameters = null): ?mixed
    {
        return $this->createLazy($id, $parameters);
    }


    /**
     * @param string     $id
     * @param array|null $parameters
     * @return mixed|null
     * @throws \RuntimeException
     */
    private function createLazy(string $id, array $parameters = null): ?mixed
    {
        if ($this->has($id)) {
            return $this->get($id);
        }

        if (!class_exists($id)) {
            throw new \RuntimeException('Classe não existe para injeção no container: ' . $id);
        }

        $this->set($id, $this->lazyFunction($id, $parameters));

        return $this->get($id);
    }


    /**
     * @param string     $id
     * @param array|null $parameters
     * @return Closure
     */
    private function lazyFunction(string $id, array $parameters = null): Closure
    {
        return function () use ($id, $parameters) {
            $class = new \ReflectionClass($id);

            return $class->hasMethod('__construct')
                ? $class->newInstanceArgs($parameters ?: [])
                : $class->newInstanceWithoutConstructor();
        };
    }


    /**
     * @param array $items
     */
    public function addDependencies(array $items): void
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }


    /**
     * Retrieve an external iterator
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->values);
    }


    /**
     * @param string $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }


    /**
     * Unset an offset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        if ($this->has($offset)) {
            unset($this->values[(string)$offset]);
        }
    }


    /**
     * Whether an offset exists
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }


    /**
     * Offset to retrieve
     *
     * @param string $offset
     * @return mixed|null
     */
    public function offsetGet($offset): ?mixed
    {
        return $this->get($offset);
    }
}
