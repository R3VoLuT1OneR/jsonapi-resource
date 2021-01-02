<?php

use \JSONAPI\Resource\Cache\ArrayCache;
use \JSONAPI\Resource\Cache\InvalidKeyException;
use \JSONAPI\Resource\Cache\InvalidTTLException;

test('array cache usage', function () {
    $cache = new ArrayCache();

    expect($cache->set('f', 'fvalue', 10))
        ->toBeTrue()
        ->and($cache->has('f'))
        ->toBeTrue()
        ->and($cache->get('f'))
        ->toBe('fvalue')
        ->and($cache->delete('f'))
        ->toBeTrue()
        ->and($cache->has('f'))
        ->toBeFalse()
    ;
});

test('multiple keys set', function () {
    $cache = new ArrayCache();

    expect($cache->setMultiple([
        'f1' => 'f1value',
        'f2' => 'f2value',
    ]))
        ->toBeTrue()
        ->and($cache->get('f1'))
        ->toBe('f1value')
        ->and($cache->get('f2'))
        ->toBe('f2value')
        ->and($cache->getMultiple(['f1', 'f2']))
        ->toBe(['f1value', 'f2value'])
        ->and($cache->deleteMultiple(['f2']))
        ->toBeTrue()
        ->and($cache->has('f1'))
        ->toBeTrue()
        ->and($cache->has('f2'))
        ->toBeFalse()
        ->and($cache->clear())
        ->toBeTrue()
        ->and($cache->has('f1'))
        ->toBeFalse()
        ->and($cache->has('f2'))
        ->toBeFalse()
    ;
});

test('value is expired', function () {
    class MockArrayCache extends ArrayCache {
        public bool $inFuture = false;

        protected function now(): DateTime
        {
            if ($this->inFuture) {
                return new DateTime('+15 seconds');
            }

            return new DateTime();
        }
    }

    $cache = new MockArrayCache();
    $cache->set('f', 'val', 10);

    expect($cache->get('f', 'defaultValue'))->toBe('val');

    // Put in future
    $cache->inFuture = true;

    expect($cache->get('f', 'defaultValue'))->toBe('defaultValue');
});

test('ttl must be int or dateinterval', function () {
    $cache = new ArrayCache();
    $cache->set('int', 'val', 10);
    $cache->set('dateinterval', 'val', new DateInterval('PT10S'));

    /** @phpstan-ignore-next-line  */
    $cache->set('wronginterval', 'val', '10S');
})->throws(InvalidTTLException::class);

test('key must be only string', function () {
    /** @phpstan-ignore-next-line  */
    (new ArrayCache())->set([], 'value');
})->throws(InvalidKeyException::class);

test('multiple key must be only array on set', function () {
    /** @phpstan-ignore-next-line  */
    (new ArrayCache())->setMultiple('test', 'value');
})->throws(InvalidKeyException::class);

test('multiple key must be only array on get', function () {
    /** @phpstan-ignore-next-line  */
    (new ArrayCache())->getMultiple('test');
})->throws(InvalidKeyException::class);

test('multiple key must be only array on delete', function () {
    /** @phpstan-ignore-next-line  */
    (new ArrayCache())->deleteMultiple('test');
})->throws(InvalidKeyException::class);