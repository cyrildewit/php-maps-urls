<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\ValueObjects\Coordinates;

it('exposes the latitude and the longitude', function (): void {
    $coordinates = new Coordinates(47.5951518, -122.3316393);

    expect($coordinates->latitude)->toBe(47.5951518)
        ->and($coordinates->longitude)->toBe(-122.3316393);
});

it('formats the pair with a comma', function (): void {
    expect((string) new Coordinates(47.5951518, -122.3316393))
        ->toBe('47.5951518,-122.3316393');
});

it('drops the decimals from a whole degree', function (): void {
    expect((string) new Coordinates(41, 2))->toBe('41,2');
});

it('keeps a pair on the equator and the prime meridian', function (): void {
    expect((string) new Coordinates(0, 0))->toBe('0,0');
});

it('writes a small value in full rather than exponential notation', function (): void {
    expect((string) new Coordinates(0.0000001, -0.0001))->toBe('0.0000001,-0.0001');
});

it('rounds beyond seven decimals', function (): void {
    expect((string) new Coordinates(1.123456789, 2.000000049))->toBe('1.1234568,2');
});

it('rounds a value below the seventh decimal down to zero', function (): void {
    expect((string) new Coordinates(0.00000001, 0))->toBe('0,0');
});

it('writes a negative value that rounds away as plain zero', function (): void {
    expect((string) new Coordinates(-0.00000001, -0.0))->toBe('0,0');
});
