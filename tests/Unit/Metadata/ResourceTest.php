<?php

use JSONAPI\Resource\Metadata\Resource;
use JSONAPI\Resource\Attributes\Resource as ResourceAttr;
use JSONAPI\Resource\Metadata\Exceptions\IdFetcherMissingException;

test('resource with methods', function () {

    class TestIdMethodFetcher {
        public function getResourceId(): int
        {
            return 777;
        }
    }

    $obj = new TestIdMethodFetcher();
    $resource = new Resource(
        new ResourceAttr('test', 'getResourceId'),
        new ReflectionObject($obj)
    );

    expect($resource->getType())->toBe('test');
    expect($resource->getId($obj))->toBe('777');
});

test('resource with properties', function () {
    class TestIdPropertyFetcher {
        public int $resourceId = 123;
    }

    $obj = new TestIdPropertyFetcher();
    $resource = new Resource(
        new ResourceAttr('test', 'resourceId'),
        new ReflectionObject($obj)
    );

    expect($resource->getType())->toBe('test');
    expect($resource->getId($obj))->toBe('123');
});

test('id fetcher property not exists', function () {
    $obj = new stdClass();
    $resource = new Resource(
        new ResourceAttr('test', 'id'),
        new ReflectionObject($obj)
    );
    $resource->getId($obj);
})->throws(IdFetcherMissingException::class);

test('id fetcher method not exists', function () {
    $obj = new stdClass();
    $resource = new Resource(
        new ResourceAttr('test', 'getResourceId'),
        new ReflectionObject($obj)
    );
    $resource->getId($obj);
})->throws(IdFetcherMissingException::class);
