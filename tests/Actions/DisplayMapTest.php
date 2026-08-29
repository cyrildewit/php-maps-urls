<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Actions\DisplayMap;
use CyrildeWit\MapsUrls\Coordinates;
use CyrildeWit\MapsUrls\Enums\BaseMap;
use CyrildeWit\MapsUrls\Enums\Layer;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;

it('writes the coordinates endpoint', function (): void {
    expect(new DisplayMap()->endpoint())->toBe('@');
});

it('always writes the map action', function (): void {
    expect(new DisplayMap()->parameters())->toBe([
        'map_action' => 'map',
        'center' => null,
        'zoom' => null,
        'basemap' => null,
        'layer' => null,
    ]);
});

it('writes every parameter it was given', function (): void {
    $action = new DisplayMap(
        center: new Coordinates(-33.8569, 151.2152),
        zoom: 10,
        baseMap: BaseMap::Satellite,
        layer: Layer::Transit,
    );

    expect($action->parameters())->toBe([
        'map_action' => 'map',
        'center' => '-33.8569,151.2152',
        'zoom' => 10,
        'basemap' => 'satellite',
        'layer' => 'transit',
    ]);
});

it('writes a zoom of zero', function (): void {
    expect(new DisplayMap(zoom: 0)->parameters()['zoom'])->toBe(0);
});

it('accepts a zoom at both ends of the range', function (): void {
    expect(new DisplayMap(zoom: 0)->zoom)->toBe(0)
        ->and(new DisplayMap(zoom: 21)->zoom)->toBe(21);
});

it('rejects a zoom above the highest level', function (): void {
    new DisplayMap(zoom: 22);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'zoom'. Expected from 0 to 21. Received '22'."
);

it('rejects a negative zoom', function (): void {
    new DisplayMap(zoom: -1);
})->throws(InvalidOption::class);

it('writes a layer of none rather than leaving it out', function (): void {
    expect(new DisplayMap(layer: Layer::None)->parameters()['layer'])->toBe('none');
});

it('builds from an array', function (): void {
    $action = DisplayMap::fromArray([
        'center' => [-33.8569, 151.2152],
        'zoom' => 10,
        'basemap' => BaseMap::Satellite,
        'layer' => Layer::Transit,
    ]);

    expect($action->center?->latitude)->toBe(-33.8569)
        ->and($action->center?->longitude)->toBe(151.2152)
        ->and($action->zoom)->toBe(10)
        ->and($action->baseMap)->toBe(BaseMap::Satellite)
        ->and($action->layer)->toBe(Layer::Transit);
});

it('takes a Coordinates instance as the center', function (): void {
    $center = new Coordinates(-33.8569, 151.2152);

    expect(DisplayMap::fromArray(['center' => $center])->center)->toBe($center);
});

it('rejects a center that is not a pair', function (): void {
    DisplayMap::fromArray(['center' => [-33.8569]]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'center'. Expected a [latitude, longitude] pair. Received array."
);

it('rejects a center given with keys rather than in order', function (): void {
    DisplayMap::fromArray(['center' => ['longitude' => 151.2152, 'latitude' => -33.8569]]);
})->throws(InvalidOption::class);

it('rejects a center that is not an array or a Coordinates instance', function (): void {
    DisplayMap::fromArray(['center' => '-33.8569,151.2152']);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'center'. Expected a Coordinates instance or a [latitude, longitude] pair. Received '-33.8569,151.2152'."
);

it('rejects a zoom that is not an integer', function (): void {
    DisplayMap::fromArray(['zoom' => '10']);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'zoom'. Expected an integer. Received '10'."
);
