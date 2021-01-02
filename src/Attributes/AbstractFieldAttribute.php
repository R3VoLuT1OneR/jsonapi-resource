<?php namespace JSONAPI\Resource\Attributes;

use ReflectionMethod;
use ReflectionProperty;
use ReflectionException;

abstract class AbstractFieldAttribute
{
    /**
     * Attribute constructor.
     * @param string|null           $key        Field key to be used in attributes\relationships representation.
     * @param mixed|callable|null   $value      Field value or function that may work as getter.
     *                                          Can be used for class target attributes that don't property or method
     *                                          to return the value.
     * @param array<string, mixed>  $options    Any other extra options that can be used by plugins.
     */
    public function __construct(
        protected ?string $key = null,
        protected mixed $value = null,
        protected array $options = []
    ) {}

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}