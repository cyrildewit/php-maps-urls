<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Tests\Fixtures\TestAbstractAction;

it('passes each option to its mapped setter', function () {
    $action = TestAbstractAction::make([
        'string' => 'foo',
        'int' => 22,
        'float' => 6.84,
        'array' => ['foo', 'bar'],
    ]);

    expect($action->getStringProp())->toBe('foo')
        ->and($action->getIntProp())->toBe(22)
        ->and($action->getFloatProp())->toBe(6.84)
        ->and($action->getArrayProp())->toBe(['foo', 'bar']);
});

it('ignores options without a mapped setter', function () {
    $action = TestAbstractAction::make([
        'string' => 'foo',
        'unmapped' => 'bar',
    ]);

    expect($action->getStringProp())->toBe('foo');
});

it('spreads an array over a setter that takes more than one argument', function () {
    $action = TestAbstractAction::make(['pair' => [52.1, 4.2]]);

    expect($action->getPairProp())->toBe('52.1,4.2');
});

it('spreads a keyed array as named arguments', function () {
    $action = TestAbstractAction::make(['pair' => ['second' => 4.2, 'first' => 52.1]]);

    expect($action->getPairProp())->toBe('52.1,4.2');
});

it('passes an array whole to a setter that takes one argument', function () {
    $action = TestAbstractAction::make(['array' => ['foo', 'bar']]);

    expect($action->getArrayProp())->toBe(['foo', 'bar']);
});

it('returns an instance of the action it was called on', function () {
    expect(TestAbstractAction::make([]))->toBeInstanceOf(TestAbstractAction::class);
});
