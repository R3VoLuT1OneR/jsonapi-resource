<?php namespace JSONAPI\Resource\LinkGenerator;

use JSONAPI\Resource\Attributes\Relationship;
use JSONAPI\Resource\Metadata\Repository;

class BasicLinksGenerator implements LinkGeneratorInterface
{
    public function __construct(
        protected ?Repository $metadata,
        protected string $baseUrl = '',
    ) {
        $this->metadata ??= new Repository();
    }

    /**
     * @param object $resource
     * @return array<string, string>|null
     */
    public function resourceLinks(object $resource): array | null
    {
        $meta = $this->metadata->getResourceMeta($resource);
        $type = $meta->getType();
        $id = $meta->getId($resource);

        return [
            'self' => sprintf('%s/%s/%s', $this->baseUrl, $type, $id),
        ];
    }

    /**
     * @param object $resource
     * @param string $relationship
     * @return array<string, string>|null
     */
    public function relationshipLinks(object $resource, string $relationship): array | null
    {
        $links = [];

        $meta = $this->metadata->getResourceMeta($resource);
        $type = $meta->getType();
        $id = $meta->getId($resource);

        $relationshipsOpts = $this->metadata->getResourceRelationships($resource)[$relationship]->getOptions();

        if (($relationshipsOpts[Relationship::OPTIONS_NO_SELF_LINK] ?? false) === false) {
            $links['self'] = sprintf('%s/%s/%s/relationships/%s', $this->baseUrl, $type, $id, $relationship);
        }

        if (($relationshipsOpts[Relationship::OPTIONS_NO_RELATED_LINK] ?? false) === false) {
            $links['related'] = sprintf('%s/%s/%s/%s', $this->baseUrl, $type, $id, $relationship);
        }

        return empty($links) ? null : $links;
    }
}