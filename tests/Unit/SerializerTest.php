<?php

use JSONAPI\Resource\Includeset;
use JSONAPI\Resource\Serializer;
use JSONAPI\Resource\LinkGenerator\BasicLinksGenerator;
use JSONAPI\Resource\Metadata\Repository;

use JSONAPI\Resource\Tests\Models\Article;
use JSONAPI\Resource\Tests\Models\People;
use JSONAPI\Resource\Tests\Models\Comment;

test('JSONAPI - Example from https://jsonapi.org serialization', function () {

    $expected = \json_decode(
        (string) file_get_contents(__DIR__ . '/../fixtures/jsonapi_dot_org_example.json'),
        true
    );

    $articles = [
        new Article(1, new People(9), [
            new Comment(5, new People(2), 'First!'),
            new Comment(12, new People(9), 'I like XML better')
        ])
    ];

    $includeset = Includeset::fromString('author,comments.author');

    $metadataRepository = new Repository();
    $linkGenerator = new BasicLinksGenerator($metadataRepository, 'http://example.com');
    $serializer = new Serializer(
        includeset: $includeset,
        linksGenerator: $linkGenerator
    );

    expect($serializer->serialize($articles))
        ->toHaveCount(1)
        ->toMatchArray($expected['data'])
        ->and($serializer->compoundData())
        ->toHaveCount(3)
        ->toMatchArray($expected['included']);

});
