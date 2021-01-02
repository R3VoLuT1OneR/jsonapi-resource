<?php namespace JSONAPI\Resource\Tests\Models;

use JSONAPI\Resource\Attributes as JSONAPI;

#[JSONAPI\Resource('articles')]
class Article
{
    /**
     * Article constructor.
     * @param int $id
     * @param People $author
     * @param Comment[] $comments
     */
    public function __construct(
        public int $id,
        protected People $author,
        protected array $comments = []
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    #[JSONAPI\Attribute()]
    public string $title = 'JSON:API paints my bikeshed!';

    #[JSONAPI\Relationship('author')]
    public function getAuthor(): ?People
    {
        return $this->author;
    }

    /**
     * @return Comment[]
     */
    #[JSONAPI\Relationship()]
    public function comments(): array
    {
        return $this->comments;
    }
}