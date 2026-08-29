<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Coordinates;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;

it('exposes the latitude and the longitude', function (): void {
    $coordinates = new Coordinates(47.5951518, -122.3316393);

    expect($coordinates->latitude)->toBe(47.5951518)
        ->and($coordinates->longitude)->toBe(-122.3316393);
});

it('formats the pair with a comma', function (): void {
    expect(new Coordinates(47.5951518, -122.3316393)->format())
        ->toBe('47.5951518,-122.3316393');
});

it('formats the pair when cast to a string', function (): void {
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

it('accepts a latitude and a longitude at their bounds', function (): void {
    expect((string) new Coordinates(-90, -180))->toBe('-90,-180')
        ->and((string) new Coordinates(90, 180))->toBe('90,180');
});

it('rejects a latitude outside the poles', function (): void {
    new Coordinates(999, 0);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'latitude'. Expected from -90 to 90. Received '999'."
);

it('rejects a longitude past the antimeridian', function (): void {
    new Coordinates(0, -500);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'longitude'. Expected from -180 to 180. Received '-500'."
);

it('skips the range check when constructed unchecked', function (): void {
    $coordinates = Coordinates::unchecked(999, -500);

    expect($coordinates->latitude)->toBe(999.0)
        ->and($coordinates->longitude)->toBe(-500.0)
        ->and((string) $coordinates)->toBe('999,-500');
});
