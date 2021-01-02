<?php namespace JSONAPI\Resource\Metadata;

use JSONAPI\Resource\Attributes\AbstractFieldAttribute;

use JSONAPI\Resource\Metadata\Exceptions\ClassesNotMatchException;
use JSONAPI\Resource\Metadata\Exceptions\KeyMissingException;
use JSONAPI\Resource\Metadata\Exceptions\ValueMissingException;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

/**
 * Wrapper around the "Attribute" attribute.
 * Represents specific resource JSONAPI attribute.
 */
class Field
{
    public function __construct(
        protected AbstractFieldAttribute $attribute,
        protected Reflector $reflection
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->attribute->getOptions();
    }

    /**
     * Returns attribute key in JSON:API resource presentation: data.attribute.{key}
     *
     * @return string
     */
    public function getKey(): string
    {
        if (null !== ($key = $this->attribute->getKey())) {
            return $key;
        }

        if ($this->reflection instanceof ReflectionMethod) {
            return $this->reflection->getName();
        }

        if ($this->reflection instanceof ReflectionProperty) {
            return $this->reflection->getName();
        }

        throw new KeyMissingException('Unable to get key. Probably key not provided for class target.');
    }

    /**
     * Method used for fetching attribute value from JSON:API resource.
     */
    public function getValue(object $resource): mixed
    {
        if (null !== ($value = $this->attribute->getValue())) {
            return is_callable($value) ? $value($resource, $this) : $value;
        }

        if ($this->reflection instanceof ReflectionMethod) {
            if (!$this->reflection->getDeclaringClass()->isInstance($resource)) {
                throw new ClassesNotMatchException(sprintf(
                    'Provided resource "%s" do not match field\'s parent class "%s"',
                    get_class($resource),
                    $this->reflection->getDeclaringClass()->getName()
                ));
            }

            return $this->reflection->invoke($resource);
        }

        if ($this->reflection instanceof ReflectionProperty) {
            return $this->reflection->getValue($resource);
        }

        throw new ValueMissingException('Unable to get value. Probably value not provided for class target.');
    }
}