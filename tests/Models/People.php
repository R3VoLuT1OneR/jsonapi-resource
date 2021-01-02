<?php namespace JSONAPI\Resource\Tests\Models;

use JSONAPI\Resource\Attributes as JSONAPI;

#[JSONAPI\Resource('people')]
class People
{

    #[JSONAPI\Attribute()]
    public string $firstName = 'Dan';

    #[JSONAPI\Attribute()]
    public string $lastName = 'Gebhardt';

    #[JSONAPI\Attribute()]
    public string $twitter = 'dgeb';

    public function __construct(
        public int $id = 1,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }
}