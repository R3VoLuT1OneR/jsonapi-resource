<?php namespace JSONAPI\Resource\Tests\Models;

use JSONAPI\Resource\Attributes;

#[Attributes\Resource('comments')]
class Comment
{
    #[Attributes\Relationship(
        options: [
            Attributes\Relationship::OPTIONS_NO_SELF_LINK => true,
            Attributes\Relationship::OPTIONS_NO_RELATED_LINK => true,
        ]
    )]
    public People $author;

    #[Attributes\Attribute]
    public string $body;

    public function __construct(
        public int $id,
        People $author,
        string $body
    ) {
        $this->author = $author;
        $this->body = $body;
    }

    public function getId(): int
    {
        return $this->id;
    }
}