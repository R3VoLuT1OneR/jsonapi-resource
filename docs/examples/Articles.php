<?php

use JSONAPI\Resource\Attributes as JSONAPI;

#[JSONAPI\Resource('articles')]
class Articles
{
    public function __construct(
        protected int $id,
        protected string $title,
        protected People $author,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    #[JSONAPI\Attribute()]
    public function getTitle(): string
    {
        return $this->title;
    }

    #[JSONAPI\Relationship('author')]
    public function getAuthor(): ?People
    {
        return $this->author;
    }
}