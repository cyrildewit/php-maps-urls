<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
use CyrildeWit\MapsUrls\Tests\Fixtures\TestAbstractAction;

it('passes each option to its mapped setter', function (): void {
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

it('rejects an option without a mapped setter', function (): void {
    TestAbstractAction::make([
        'string' => 'foo',
        'unmapped' => 'bar',
    ]);
})->throws(
    InvalidOption::class,
    "Unknown option 'unmapped'. Expected one of 'string', 'int', 'float', 'array', 'pair'."
);

it('spreads an array over a setter that takes more than one argument', function (): void {
    $action = TestAbstractAction::make(['pair' => [52.1, 4.2]]);

    expect($action->getPairProp())->toBe('52.1,4.2');
});

it('spreads a keyed array as named arguments', function (): void {
    $action = TestAbstractAction::make(['pair' => ['second' => 4.2, 'first' => 52.1]]);

    expect($action->getPairProp())->toBe('52.1,4.2');
});

it('passes an array whole to a setter that takes one argument', function (): void {
    $action = TestAbstractAction::make(['array' => ['foo', 'bar']]);

    expect($action->getArrayProp())->toBe(['foo', 'bar']);
});

it('returns an instance of the action it was called on', function (): void {
    expect(TestAbstractAction::make([]))->toBeInstanceOf(TestAbstractAction::class);
});
