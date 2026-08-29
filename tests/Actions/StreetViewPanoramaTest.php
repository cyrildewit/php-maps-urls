<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Actions\StreetViewPanorama;
use CyrildeWit\MapsUrls\Coordinates;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;

it('writes the coordinates endpoint', function (): void {
    expect(new StreetViewPanorama(panoramaId: 'tu510ie_z4ptBZYo2BGEJg')->endpoint())->toBe('@');
});

it('always writes the map action', function (): void {
    expect(new StreetViewPanorama(panoramaId: 'tu510ie_z4ptBZYo2BGEJg')->parameters())->toBe([
        'map_action' => 'pano',
        'viewpoint' => null,
        'pano' => 'tu510ie_z4ptBZYo2BGEJg',
        'heading' => null,
        'pitch' => null,
        'fov' => null,
    ]);
});

it('points the camera with a viewpoint alone', function (): void {
    $action = new StreetViewPanorama(viewpoint: new Coordinates(48.857832, 2.295226));

    expect($action->parameters()['viewpoint'])->toBe('48.857832,2.295226')
        ->and($action->parameters()['pano'])->toBeNull();
});

it('rejects a panorama with nothing to point the camera at', function (): void {
    new StreetViewPanorama(heading: 120);
})->throws(
    InvalidOption::class,
    "Missing option. Google requires 'viewpoint' or 'pano'."
);

it('writes every parameter it was given', function (): void {
    $action = new StreetViewPanorama(
        viewpoint: new Coordinates(48.857832, 2.295226),
        panoramaId: 'tu510ie_z4ptBZYo2BGEJg',
        heading: 120,
        pitch: 40,
        fov: 80,
    );

    expect($action->parameters())->toBe([
        'map_action' => 'pano',
        'viewpoint' => '48.857832,2.295226',
        'pano' => 'tu510ie_z4ptBZYo2BGEJg',
        'heading' => 120,
        'pitch' => 40,
        'fov' => 80,
    ]);
});

it('accepts a heading at both ends of the range', function (): void {
    expect(new StreetViewPanorama(panoramaId: 'tu510ie_z4ptBZYo2BGEJg', heading: -180)->heading)->toBe(-180)
        ->and(new StreetViewPanorama(panoramaId: 'tu510ie_z4ptBZYo2BGEJg', heading: 360)->heading)->toBe(360);
});

it('rejects a heading outside the range', function (): void {
    new StreetViewPanorama(panoramaId: 'tu510ie_z4ptBZYo2BGEJg', heading: 361);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'heading'. Expected from -180 to 360. Received '361'."
);

it('rejects a heading below the range', function (): void {
    new StreetViewPanorama(panoramaId: 'tu510ie_z4ptBZYo2BGEJg', heading: -181);
})->throws(InvalidOption::class);

it('accepts a pitch at both ends of the range', function (): void {
    expect(new StreetViewPanorama(panoramaId: 'tu510ie_z4ptBZYo2BGEJg', pitch: -90)->pitch)->toBe(-90)
        ->and(new StreetViewPanorama(panoramaId: 'tu510ie_z4ptBZYo2BGEJg', pitch: 90)->pitch)->toBe(90);
});

it('rejects a pitch outside the range', function (): void {
    new StreetViewPanorama(panoramaId: 'tu510ie_z4ptBZYo2BGEJg', pitch: 91);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'pitch'. Expected from -90 to 90. Received '91'."
);

it('accepts a fov at both ends of the range', function (): void {
    expect(new StreetViewPanorama(panoramaId: 'tu510ie_z4ptBZYo2BGEJg', fov: 10)->fov)->toBe(10)
        ->and(new StreetViewPanorama(panoramaId: 'tu510ie_z4ptBZYo2BGEJg', fov: 100)->fov)->toBe(100);
});

it('rejects a fov outside the range', function (): void {
    new StreetViewPanorama(panoramaId: 'tu510ie_z4ptBZYo2BGEJg', fov: 101);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'fov'. Expected from 10 to 100. Received '101'."
);

it('builds from an array', function (): void {
    $action = StreetViewPanorama::fromArray([
        'viewpoint' => [48.857832, 2.295226],
        'pano' => 'tu510ie_z4ptBZYo2BGEJg',
        'heading' => 120,
        'pitch' => 40,
        'fov' => 80,
    ]);

    expect($action->parameters())->toBe([
        'map_action' => 'pano',
        'viewpoint' => '48.857832,2.295226',
        'pano' => 'tu510ie_z4ptBZYo2BGEJg',
        'heading' => 120,
        'pitch' => 40,
        'fov' => 80,
    ]);
});

it('rejects a panorama id that is not a string', function (): void {
    StreetViewPanorama::fromArray(['pano' => 42]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'pano'. Expected a string. Received 42."
);
