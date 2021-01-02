<?php namespace JSONAPI\Resource\Metadata;

use JSONAPI\Resource\Metadata\Resource as ResourceMeta;

use JSONAPI\Resource\Cache\ArrayCache;
use Psr\SimpleCache\CacheInterface;

/**
 * Class used for getting metadata from JSONAPI resource objects.
 * To get best performance from all the JSONAPI resource library, cache must be used and same metadata object reused
 * between serializers or event requests.
 */
class Repository
{
    const CACHE_KEY_RESOURCE = 'class::resource';
    const CACHE_KEY_ATTRIBUTES = 'class::attributes';
    const CACHE_KEY_RELATIONSHIPS = 'class::relationships';

    public function __construct(
        protected ?CacheInterface $cache = null,
        protected ?Factory $factory = null
    ) {
        $this->cache ??= new ArrayCache();
        $this->factory ??= new Factory();
    }

    /**
     * Get object resource metadata.
     *
     * @param object $resource
     * @return ResourceMeta
     */
    public function getResourceMeta(object $resource): ResourceMeta
    {
        $key = get_class($resource).'::'.static::CACHE_KEY_RESOURCE;

        if (null === ($result = $this->cache->get($key))) {
            $result = $this->factory->buildResourceMeta($resource);

            $this->cache->set($key, $result);
        }

        return $result;
    }

    /**
     * Get resource mapped attributes.
     * Returns map of property\method reflection mapped to it is resource attribute.
     *
     * @param object $resource
     * @return Field[]
     */
    public function getResourceAttributes(object $resource): array
    {
        $key = get_class($resource).'::'.static::CACHE_KEY_ATTRIBUTES;

        if (null === ($result = $this->cache->get($key))) {
            $result = [];
            foreach ($this->factory->buildResourceAttributes($resource) as $attribute) {
                $result[$attribute->getKey()] = $attribute;
            }

            $this->cache->set($key, $result);
        }

        return $result;
    }

    /**
     * Get resource mapped relationships.
     * Returns map of property\method reflection mapped to it is resource attribute.
     *
     * @param object $resource
     * @return Field[]
     */
    public function getResourceRelationships(object $resource): array
    {
        $key = get_class($resource).'::'.static::CACHE_KEY_RELATIONSHIPS;

        if (null === ($result = $this->cache->get($key))) {
            $result = [];
            foreach ($this->factory->buildResourceRelationships($resource) as $relationship) {
                $result[$relationship->getKey()] = $relationship;
            }

            $this->cache->set($key, $result);
        }

        return $result;
    }
}