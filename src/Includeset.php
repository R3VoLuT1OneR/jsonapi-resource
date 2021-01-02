<?php namespace JSONAPI\Resource;

/**
 * Class helper for working with `include` JSON:API parameter.
 *
 * @link https://jsonapi.org/format/#fetching-includes
 *
 * @implements \ArrayAccess<string, Includeset>
 * @implements \IteratorAggregate<string, Includeset>
 */
final class Includeset implements \ArrayAccess, \IteratorAggregate, \Countable
{
    const ITEM_SEPARATOR = ',';
    const RELATION_SEPARATOR = '.';

    /** @var array<string, Includeset> */
    protected array $relations = [];

    public static function fromString(string $raw): Includeset
    {
        $include = new Includeset();
        $items = array_unique(explode(Includeset::ITEM_SEPARATOR, $raw));

        foreach ($items as $item) {
            $parserInclude = explode(Includeset::RELATION_SEPARATOR, $item, 2);
            $relation = array_shift($parserInclude);

            if (empty($relation)) {
                continue;
            }

            $rawChildren = array_shift($parserInclude) ?? '';

            if (isset($include->relations[$relation])) {
                $include->relations[$relation] = $include
                    ->relations[$relation]
                    ->withInclude(Includeset::fromString($rawChildren));

                continue;
            }

            $include->relations[$relation] = Includeset::fromString($rawChildren);
        }

        return $include;
    }

    public function withRelation(string $relation): self
    {
        if (isset($this[$relation])) {
            return $this;
        }

        $new = clone $this;
        $new->relations[$relation] = new Includeset();

        return $new;
    }

    public function withInclude(self $includeset): self
    {
        $new = clone $this;

        foreach ($includeset as $relation => $item) {
            if (isset($new->relations[$relation])) {
                $new->relations[$relation] = $new
                    ->relations[$relation]
                    ->withInclude($item);

                continue;
            }

            $new->relations[$relation] = $item;
        }

        return $new;
    }

    public function count() : int
    {
        return count($this->relations);
    }

    public function offsetExists($key): bool
    {
        return isset($this->relations[$key]);
    }

    public function offsetGet($key): ?Includeset
    {
        return isset($this->relations[$key]) ? $this->relations[$key] : null;
    }

    public function offsetSet($key, $value)
    {
        throw new \LogicException('Modifying includeset is not permitted');
    }

    public function offsetUnset($key)
    {
        throw new \LogicException('Modifying includeset is not permitted');
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->relations);
    }
}