<?php namespace JSONAPI\Resource;

use JSONAPI\Resource\LinkGenerator\BasicLinksGenerator;
use JSONAPI\Resource\LinkGenerator\LinkGeneratorInterface;
use JSONAPI\Resource\Metadata\Repository;

class Serializer
{
    /** @var array[] */
    protected array $compoundData = [];

    // Skip creating compound data.
    const OPTIONS_NO_COMPOUND_DOCUMENTS = 'noCompoundDocuments';

    // Skip attributes serialization.
    const OPTIONS_NO_ATTRIBUTES = 'noAttributes';

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        protected ?Repository $metadata = null,
        protected ?Fieldset $fieldset = null,
        protected ?Includeset $includeset = null,
        protected ?LinkGeneratorInterface $linksGenerator = null,
        protected array $options = [],
    ) {
        $this->metadata ??= new Repository();
        $this->linksGenerator ??= new BasicLinksGenerator($this->metadata);
    }

    /**
     * @param object|object[] $resource
     * @return array<string, array>|array<array<string, array>>
     */
    public function serialize(object | array $resource): array
    {
        return is_array($resource)
            ? array_map(fn($resource) => $this->serializeResource($resource), $resource)
            : $this->serializeResource($resource);
    }

    /**
     * @return array[]
     */
    public function compoundData(): array
    {
        return array_values((array) $this->compoundData);
    }

    /**
     * @param object $resource
     * @return array<string, string|array>
     */
    protected function serializeResource(object $resource): array
    {
        $meta = $this->metadata->getResourceMeta($resource);
        $type = $meta->getType();
        $id = $meta->getId($resource);

        $data = [
            'type' => $type,
            'id' => $id,
        ];

        if (null !== ($attributes = $this->serializeAttributes($resource, $this->fieldset[$type] ?? null))) {
            $data['attributes'] = $attributes;
        }

        if (null !== ($relationships = $this->serializeRelationships($resource))) {
            $data['relationships'] = $relationships;
        }

        if (null !== ($links = $this->linksGenerator->resourceLinks($resource))) {
            $data['links'] = $links;
        }

        return $data;
    }

    /**
     * @param object $resource
     * @param string[]|null $fields
     * @return array<string, mixed>|null
     */
    protected function serializeAttributes(object $resource, array | null $fields): array | null
    {
        if (($this->options[static::OPTIONS_NO_ATTRIBUTES] ?? false) === true) {
            return null;
        }

        $result = [];

        $attributes = array_filter(
            $this->metadata->getResourceAttributes($resource),
            fn($key) => is_null($fields) || in_array($key, $fields)
        );

        foreach ($attributes as $key => $attribute) {
            $result[$key] = $attribute->getValue($resource);
        }

        return $result;
    }

    /**
     * @param object $resource
     * @return array<string, array>|null
     */
    protected function serializeRelationships(object $resource): array | null
    {
        if ($this->includeset === null || count($this->includeset) === 0) {
            return null;
        }

        $relationships = [];
        $relationshipsMap = $this->metadata->getResourceRelationships($resource);

        foreach ($this->includeset as $relation => $childIncludeset) {
            if (!isset($relationshipsMap[$relation])) {
                // TODO: Maybe we should throw an exception here
                continue;
            }

            $value = $relationshipsMap[$relation]->getValue($resource);

            $relationships[$relation] = [
                'data' => is_array($value)
                    ? array_map(fn($val) => $this->compoundedDocument($val, $childIncludeset), $value)
                    : $this->compoundedDocument($value, $childIncludeset),
            ];

            if (null !== ($links = $this->linksGenerator->relationshipLinks($resource, $relation))) {
                $relationships[$relation]['links'] = $links;
            }
        }

        return $relationships;
    }

    /**
     * @param object $resource
     * @param Includeset $includeset
     * @return array<string, string>
     */
    protected function compoundedDocument(object $resource, Includeset $includeset): array
    {
        $meta = $this->metadata->getResourceMeta($resource);
        $type = $meta->getType();
        $id = $meta->getId($resource);

        if (($this->options[static::OPTIONS_NO_COMPOUND_DOCUMENTS] ?? false) === false) {
            $objKey = sprintf('%s-%s', $type, $id);
            if (!isset($this->compoundData[$objKey])) {
                $serializer = clone $this;
                $serializer->includeset = $includeset;
                $serializer->metadata = $this->metadata;

                $this->compoundData[$objKey] = $serializer->serialize($resource);
            }
        }

        return [
            'type' => $type,
            'id' => $id,
        ];
    }
}
