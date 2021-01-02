<?php

use JSONAPI\Resource\Attributes as JSONAPI;

#[JSONAPI\Resource('people')]
class People
{
    public function __construct(
        protected int $id,
        protected string $name,
        protected int $age
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    #[JSONAPI\Attribute('name')]
    public function getName(): string
    {
        return $this->name;
    }

    #[JSONAPI\Attribute('name')]
    public function getAge(): int
    {
        return $this->age;
    }
}