<?php

use JSONAPI\Resource\Fieldset;

test('parse basic spare fieldset', function () {
    // fields[articles]=title,body&fields[people]=name
    $fields = [
        'articles' => 'title,body',
        'people' => 'name',
    ];

    $fieldset = new Fieldset($fields);

    expect($fieldset)
        ->toHaveCount(2)
        ->and($fieldset['articles'])
            ->toHaveCount(2)
            ->and($fieldset['articles'][0])
                ->toBe('title')
            ->and($fieldset['articles'][1])
                ->toBe('body')
        ->and($fieldset['people'])
            ->toHaveCount(1)
            ->and($fieldset['people'][0])
                ->toBe('name')
    ;

    expect($fieldset->hasFieldset('people'))->toBeTrue();
    expect($fieldset->hasFieldset('articles'))->toBeTrue();
    expect($fieldset->hasFieldset('test'))->toBeFalse();

    expect(new Fieldset())->toHaveCount(0);
});

test('key mst be string', function () {
    /** @phpstan-ignore-next-line  */
    $fieldset = new Fieldset(['test']); // NOSONAR
})->throws(InvalidArgumentException::class, 'Provided fieldset key is not a string.');

test('fieldset must be an array', function () {
    /** @phpstan-ignore-next-line  */
    $fieldset = new Fieldset(['test' => new stdClass()]); // NOSONAR
})->throws(InvalidArgumentException::class, 'Provided fieldset value for type "test" is not string and not array.');

test('fieldset can\'t be changed', function () {
    $fieldset = new Fieldset(['test' => 'testField']);
    $fieldset['test2'] = ['test'];
})->throws(LogicException::class, 'Modifying fieldset is not permitted');

test('fieldset field can be removed', function () {
    $fieldset = new Fieldset(['test' => 'testField']);
    unset($fieldset['test']);
})->throws(LogicException::class, 'Modifying fieldset is not permitted');