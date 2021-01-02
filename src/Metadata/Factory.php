<?php namespace JSONAPI\Resource\Metadata;

use JSONAPI\Resource\Attributes\AbstractFieldAttribute;
use JSONAPI\Resource\Attributes\Attribute;

use JSONAPI\Resource\Attributes\Relationship;
use JSONAPI\Resource\Attributes\Resource as AttrResource;
use JSONAPI\Resource\Metadata\Resource as ResourceMeta;
use ReflectionAttribute;
use ReflectionObject;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

class Factory
{
    /**
     * @param object $resource
     * @return ResourceMeta
     */
    public function buildResourceMeta(object $resource): ResourceMeta
    {
        $ref = new \ReflectionObject($resource);

        /** @var AttrResource[] $attrs */
        $attrs = array_map(fn($attr) => $attr->newInstance(), $ref->getAttributes(AttrResource::class));

        if (empty($attrs)) {
            throw new \InvalidArgumentException(sprintf(
                'The resource must have "%s" attribute.',
                AttrResource::class
            ));
        }

        return new ResourceMeta(current($attrs), $ref);
    }

    /**
     * @param object $resource
     *
     * @return Field[]
     */
    public function buildResourceAttributes(object $resource): array
    {
        $attributes = [];

        foreach ($this->possibleFieldsReflections($resource) as $ref) {
            $refAttributes = $this->buildFieldByAttribute($ref, Attribute::class);
            $attributes = array_merge($attributes, $refAttributes);
        }

        return $attributes;
    }

    /**
     * @param object $resource
     * @return Field[]
     */
    public function buildResourceRelationships(object $resource): array
    {
        $relationships = [];

        foreach ($this->possibleFieldsReflections($resource) as $ref) {
            $refRelationships = $this->buildFieldByAttribute($ref, Relationship::class);
            $relationships = array_merge($relationships, $refRelationships);
        }

        return $relationships;
    }

    /**
     * Find reflection attributes and generate metadata objects from it.
     *
     * @param ReflectionObject | ReflectionMethod | ReflectionProperty $ref
     * @param string $attributeClass
     * @return Field[]
     */
    private function buildFieldByAttribute(Reflector $ref, string $attributeClass): array
    {
        return array_map(
            // Create meta field
            fn(AbstractFieldAttribute $field) => new Field($field, $ref),

            // Find and convert reflection attribute to attribute instance.
            array_map(
                fn($attr) => $attr->newInstance(),
                $ref->getAttributes($attributeClass, ReflectionAttribute::IS_INSTANCEOF)
            ),
        );
    }

    /**
     * Return reflections of possible places where attribute can be assigned.
     *
     * @param object $resource
     * @return ReflectionObject[] | ReflectionMethod[] | ReflectionProperty[]
     */
    private function possibleFieldsReflections(object $resource): array
    {
        $objRef = new ReflectionObject($resource);

        return array_merge(
            [$objRef],
            $objRef->getMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC),
            $objRef->getProperties(ReflectionProperty::IS_STATIC | ReflectionProperty::IS_PUBLIC),
        );
    }
}