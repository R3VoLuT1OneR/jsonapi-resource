<?php namespace JSONAPI\Resource\Attributes;

use JetBrains\PhpStorm\Pure;
use JSONAPI\Resource\Attributes\AbstractFieldAttribute;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionException;

/**
 * Attribute used to mark method or property as JSON:API resource relation.
 */
#[\Attribute(
    \Attribute::IS_REPEATABLE |
    \Attribute::TARGET_METHOD |
    \Attribute::TARGET_PROPERTY |
    \Attribute::TARGET_CLASS
)]
class Relationship extends AbstractFieldAttribute
{
    const OPTIONS_NO_SELF_LINK = 'noSelfLink';
    const OPTIONS_NO_RELATED_LINK = 'noRelatedLink';
}