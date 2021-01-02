<?php namespace JSONAPI\Resource\Metadata;

use \JSONAPI\Resource\Attributes\Resource as ResourceAttr;
use JSONAPI\Resource\Metadata\Exceptions\IdFetcherMissingException;

use ReflectionClass;

class Resource
{
    /**
     * @param ReflectionClass<object> $ref
     */
    public function __construct(
        protected ResourceAttr $attribute,
        protected ReflectionClass $ref
    ) {}

    public function getType(): string
    {
        if (null === ($type = $this->attribute->getType())) {
            return lcfirst(str_replace('_', '', $this->ref->getName()));
        }

        return $type;
    }

    public function getId(object $resource): string
    {
        $idFetcher = $this->attribute->getIdFetcher();

        if (method_exists($resource, $idFetcher)) {
            return (string) $resource->{$idFetcher}();
        }

        if (property_exists($resource, $idFetcher)) {
            return (string) $resource->{$idFetcher};
        }

        throw new IdFetcherMissingException();
    }
}