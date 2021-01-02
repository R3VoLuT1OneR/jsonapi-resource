<?php namespace JSONAPI\Resource\LinkGenerator;

use JSONAPI\Resource\Metadata\Resource;

interface LinkGeneratorInterface
{
    /**
     * Provides links data for resource.
     * Example:
     *   {
     *     self: "/user/1"
     *   }
     *
     * Return null if don't want to append links.
     * Also links can be extended with new members.
     *
     * @param object $resource
     *
     * @return array<string, string>|null
     */
    public function resourceLinks(object $resource): array | null;

    /**
     * Provides links data for resource relation.
     * Example:
     *  {
     *      self: "/user/1/relationships/relation",
     *      related: "/user/1/relation"
     *  }
     *
     * Return null if don't want to append links.
     * Also links can be extended with new members.
     *
     * @param object $resource
     * @param string $relationship
     *
     * @return array<string, string>|null
     */
    public function relationshipLinks(object $resource, string $relationship): array | null;
}