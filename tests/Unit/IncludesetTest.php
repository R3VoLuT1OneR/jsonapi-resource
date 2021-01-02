<?php

use JSONAPI\Resource\Includeset;

test('Basic include parsing', function () {

    $includeset = Includeset::fromString('test');

    expect($includeset)
        ->toBeInstanceOf(Includeset::class)
        ->toHaveCount(1)
        ->and($includeset['test'])
        ->toBeInstanceOf(Includeset::class)
        ->toHaveCount(0);

});

test('Parse single child relationships', function () {

    $includeset = Includeset::fromString('test.child.child');

    expect($includeset)

        ->toBeInstanceOf(Includeset::class)
        ->toHaveCount(1)

        ->and($includeset['test'])
        ->toBeInstanceOf(Includeset::class)
        ->toHaveCount(1)

            ->and($includeset['test']['child'])
            ->toBeInstanceOf(Includeset::class)
            ->toHaveCount(1)

                ->and($includeset['test']['child']['child'])
                ->toBeInstanceOf(Includeset::class)
                ->toHaveCount(0)
    ;

});

test('Parse child relationships', function () {

    $includeset = Includeset::fromString('test.child,test.child.child,test.child2');

    expect($includeset)

        ->toBeInstanceOf(Includeset::class)
        ->toHaveCount(1)

        ->and($includeset['test'])
        ->toBeInstanceOf(Includeset::class)
        ->toHaveCount(2)

            ->and($includeset['test']['child2'])
            ->toBeInstanceOf(Includeset::class)
            ->toHaveCount(0)

            ->and($includeset['test']['child'])
            ->toBeInstanceOf(Includeset::class)
            ->toHaveCount(1)

                ->and($includeset['test']['child']['child'])
                ->toBeInstanceOf(Includeset::class)
                ->toHaveCount(0)
        ;

});

test('check withRelation method', function () {
    $includeset = new Includeset();

    expect($includeset)->toHaveCount(0);

    $includeset2 = $includeset->withRelation('test');

    expect($includeset)->toHaveCount(0);
    expect($includeset2)->toHaveCount(1);
    expect(isset($includeset2['test']))->toBeTrue();

    $includeset3 = $includeset2->withRelation('test');
    expect($includeset2)->toHaveCount(1);
    expect($includeset3)->toHaveCount(1);
    expect(isset($includeset3['test']))->toBeTrue();
});

test('do not allow add to includeset', function () {
    $includeset = Includeset::fromString('');
    $includeset['test'] = Includeset::fromString('test');
})->throws(LogicException::class, 'Modifying includeset is not permitted');

test('do not allow delete includeset', function () {
    $includeset = Includeset::fromString('test');
    unset($includeset['test']);
})->throws(LogicException::class, 'Modifying includeset is not permitted');
