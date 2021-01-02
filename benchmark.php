<?php

include_once __DIR__.'/vendor/autoload.php';

use JSONAPI\Resource\Includeset;
use JSONAPI\Resource\Serializer;
use JSONAPI\Resource\LinkGenerator\BasicLinksGenerator;
use JSONAPI\Resource\Metadata\Repository;

use JSONAPI\Resource\Tests\Models\Article;
use JSONAPI\Resource\Tests\Models\People;
use JSONAPI\Resource\Tests\Models\Comment;

$max_times = 1000;

function convert($size)
{
    $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
    return @round($size/pow(1024, ($i = floor(log($size, 1024)))), 2).' '.$unit[$i];
}

$articles = [
    new Article(1, new People(9), [
        new Comment(5, new People(2), 'First!'),
        new Comment(12, new People(9), 'I like XML better')
    ])
];

function test_serialize($resource) {
    $includeset = Includeset::fromString('author,comments.author');
    $metadataRepository = new Repository();
    $linkGenerator = new BasicLinksGenerator($metadataRepository, 'http://example.com');
    $serializer = new Serializer(
        includeset: $includeset,
        linksGenerator: $linkGenerator
    );
    $serializer->serialize($resource);
}

function test_serialize_shared_metadata($resource, $metadataRepository) {
    $includeset = Includeset::fromString('author,comments.author');
    $linkGenerator = new BasicLinksGenerator($metadataRepository, 'http://example.com');
    $serializer = new Serializer(
        metadata: $metadataRepository,
        includeset: $includeset,
        linksGenerator: $linkGenerator,
    );
    $serializer->serialize($resource);
}

$start_time = microtime(TRUE);
$times = 0;
$metadataRepositoryShared = new Repository();
while ($times < $max_times) {
    test_serialize_shared_metadata($articles, $metadataRepositoryShared);
    $times++;
}
$end_time = microtime(TRUE);
echo sprintf("With metadata %s times: %s seconds\n", number_format($max_times), $end_time - $start_time);
echo sprintf("Max memory %s\n", convert(memory_get_peak_usage()));
