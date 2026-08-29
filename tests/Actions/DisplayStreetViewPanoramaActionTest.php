<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;

it('exposes the panorama endpoint', function (): void {
    expect((new DisplayStreetViewPanoramaAction)->getEndpoint())
        ->toBe(DisplayStreetViewPanoramaAction::ENDPOINT);
});

it('builds the query parameters', function (): void {
    $action = (new DisplayStreetViewPanoramaAction)
        ->setViewpoint(20, 40)
        ->setPanoramaId('abcdefghijklmnopqrstuvwxyz')
        ->setHeading(100)
        ->setPitch(40)
        ->setFov(20);

    expect($action->getParameters())->toBe([
        'map_action' => 'pano',
        'viewpoint' => '20,40',
        'pano' => 'abcdefghijklmnopqrstuvwxyz',
        'heading' => 100,
        'pitch' => 40,
        'fov' => 20,
    ]);
});

it('formats the viewpoint as a comma separated pair', function (): void {
    $action = (new DisplayStreetViewPanoramaAction)->setViewpoint(20, 40);

    expect($action->getViewpoint())->toBe('20,40');
});

it('has no viewpoint until both coordinates are set', function (): void {
    $action = (new DisplayStreetViewPanoramaAction)->setViewpointLongitude(40);

    expect($action->getViewpoint())->toBeNull();
});

it('keeps a viewpoint on the equator or the prime meridian', function (): void {
    $action = (new DisplayStreetViewPanoramaAction)->setViewpoint(0, 0);

    expect($action->getViewpoint())->toBe('0,0');
});

it('builds null parameters when nothing is set', function (): void {
    expect((new DisplayStreetViewPanoramaAction)->getParameters())->toBe([
        'map_action' => DisplayStreetViewPanoramaAction::MAP_ACTION,
        'viewpoint' => null,
        'pano' => null,
        'heading' => null,
        'pitch' => null,
        'fov' => null,
    ]);
});

it('builds from options', function (): void {
    $action = DisplayStreetViewPanoramaAction::make([
        'viewpoint' => [20, 40],
        'pano' => 'abcdefghijklmnopqrstuvwxyz',
        'heading' => 100,
        'pitch' => 40,
        'fov' => 20,
    ]);

    expect($action->getViewpoint())->toBe('20,40')
        ->and($action->getPanoramaId())->toBe('abcdefghijklmnopqrstuvwxyz')
        ->and($action->getHeading())->toBe(100)
        ->and($action->getPitch())->toBe(40)
        ->and($action->getFov())->toBe(20);
});

it('accepts a heading within range', function (): void {
    $action = (new DisplayStreetViewPanoramaAction)->setHeading(300);

    expect($action->getHeading())->toBe(300);
});

it('accepts a heading on the edge of the range', function (int $degrees): void {
    expect((new DisplayStreetViewPanoramaAction)->setHeading($degrees)->getHeading())
        ->toBe($degrees);
})->with([-180, 360]);

it('rejects a heading outside the -180 to 360 range', function (int $degrees): void {
    (new DisplayStreetViewPanoramaAction)->setHeading($degrees);
})->with([-200, 361])->throws(InvalidOption::class);

it('accepts a pitch within range', function (): void {
    $action = (new DisplayStreetViewPanoramaAction)->setPitch(20);

    expect($action->getPitch())->toBe(20);
});

it('accepts a pitch on the edge of the range', function (int $degrees): void {
    expect((new DisplayStreetViewPanoramaAction)->setPitch($degrees)->getPitch())
        ->toBe($degrees);
})->with([-90, 90]);

it('rejects a pitch outside the -90 to 90 range', function (int $degrees): void {
    (new DisplayStreetViewPanoramaAction)->setPitch($degrees);
})->with([-91, 91])->throws(InvalidOption::class);

it('accepts a fov within range', function (): void {
    $action = (new DisplayStreetViewPanoramaAction)->setFov(40);

    expect($action->getFov())->toBe(40);
});

it('accepts a fov on the edge of the range', function (int $degrees): void {
    expect((new DisplayStreetViewPanoramaAction)->setFov($degrees)->getFov())
        ->toBe($degrees);
})->with([10, 100]);

it('rejects a fov outside the 10 to 100 range', function (int $degrees): void {
    (new DisplayStreetViewPanoramaAction)->setFov($degrees);
})->with([9, 120])->throws(InvalidOption::class);

it('reports the accepted heading range', function (): void {
    (new DisplayStreetViewPanoramaAction)->setHeading(400);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'heading'. Expected from -180 to 360. Received '400'."
);

it('reports the accepted pitch range', function (): void {
    (new DisplayStreetViewPanoramaAction)->setPitch(120);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'pitch'. Expected from -90 to 90. Received '120'."
);

it('reports the accepted fov range', function (): void {
    (new DisplayStreetViewPanoramaAction)->setFov(9);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'fov'. Expected from 10 to 100. Received '9'."
);
