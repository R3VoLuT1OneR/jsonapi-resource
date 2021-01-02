<?php namespace JSONAPI\Resource;

/**
 * Class simplifies the work with the JSON:API fields sets.
 *
 * Can be accessed as an array.
 * Key is an resource type and value is array with allowed fields list.
 *
 * @link https://jsonapi.org/format/#fetching-sparse-fieldsets
 *
 * @implements \ArrayAccess<string, string[]>
 * @implements \IteratorAggregate<string, string[]>
 */
final class Fieldset implements \ArrayAccess, \IteratorAggregate
{
    const SEPARATOR = ',';

    /** @var array<string, string[]>  */
    protected array $fieldSet = [];

    /**
     * @param array<string, string[]|string> $fieldSet
     */
    public function __construct(array $fieldSet = [])
    {
        foreach (array_keys($fieldSet) as $type) {
            if (!is_string($type)) {
                throw new \InvalidArgumentException('Provided fieldset key is not a string.');
            }

            if (is_string($fieldSet[$type])) {
                $fieldSet[$type] = explode(static::SEPARATOR, $fieldSet[$type]);
            }

            if (!is_array($fieldSet[$type])) {
                throw new \InvalidArgumentException(sprintf(
                    'Provided fieldset value for type "%s" is not string and not array.',
                    $type
                ));
            }
        }

        $this->fieldSet = $fieldSet;
    }

    public function hasFieldset(string $type): bool
    {
        return isset($this[$type]);
    }

    public function offsetExists($key)
    {
        return isset($this->fieldSet[$key]);
    }

    public function offsetGet($key)
    {
        return isset($this->fieldSet[$key]) ? $this->fieldSet[$key] : null;
    }

    public function offsetSet($key, $value)
    {
        throw new \LogicException('Modifying fieldset is not permitted');
    }

    public function offsetUnset($key)
    {
        throw new \LogicException('Modifying fieldset is not permitted');
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->fieldSet);
    }
}