<?php namespace JSONAPI\Resource\Attributes;

use JSONAPI\Resource\Attributes\AbstractFieldAttribute;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionException;

/**
 * Attribute used to mark method or property as JSON:API resource attribute.
 */
#[\Attribute(
    \Attribute::IS_REPEATABLE |
    \Attribute::TARGET_METHOD |
    \Attribute::TARGET_PROPERTY |
    \Attribute::TARGET_CLASS
)]
class Attribute extends AbstractFieldAttribute
{
}