<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Actions\DisplayMapAction;
use CyrildeWit\MapsUrls\Enums\BaseMap;
use CyrildeWit\MapsUrls\Enums\Layer;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
use CyrildeWit\MapsUrls\ValueObjects\Coordinates;

it('exposes the display map endpoint', function (): void {
    expect((new DisplayMapAction)->getEndpoint())->toBe(DisplayMapAction::ENDPOINT);
});

it('builds the query parameters', function (): void {
    $action = (new DisplayMapAction)
        ->setCenter(40, 40)
        ->setZoom(20)
        ->setBaseMap(BaseMap::Satellite)
        ->setLayer(Layer::Bicycling);

    expect($action->getParameters())->toBe([
        'map_action' => DisplayMapAction::MAP_ACTION,
        'center' => '40,40',
        'zoom' => 20,
        'basemap' => 'satellite',
        'layer' => 'bicycling',
    ]);
});

it('formats the center as a comma separated pair', function (): void {
    $action = (new DisplayMapAction)->setCenter(20, 40);

    expect($action->getCenter())->toBe('20,40');
});

it('accepts a Coordinates instance as the center', function (): void {
    $action = (new DisplayMapAction)->setCenter(new Coordinates(-33.8569, 151.2152));

    expect($action->getCenter())->toBe('-33.8569,151.2152');
});

it('rejects a center latitude without a longitude', function (): void {
    (new DisplayMapAction)->setCenter(-33.8569);
})->throws(
    InvalidOption::class,
    "Incomplete value provided for 'center'. Expected a longitude alongside the latitude, or a Coordinates instance."
);

it('has no center until both coordinates are set', function (): void {
    $action = (new DisplayMapAction)->setCenterLatitude(40);

    expect($action->getCenter())->toBeNull();
});

it('keeps a center on the equator or the prime meridian', function (): void {
    $action = (new DisplayMapAction)->setCenter(0, 0);

    expect($action->getCenter())->toBe('0,0');
});

it('builds null parameters when nothing is set', function (): void {
    expect((new DisplayMapAction)->getParameters())->toBe([
        'map_action' => DisplayMapAction::MAP_ACTION,
        'center' => null,
        'zoom' => null,
        'basemap' => null,
        'layer' => null,
    ]);
});

it('builds the center from options', function (): void {
    $action = DisplayMapAction::make(['center' => [52.1, 4.2]]);

    expect($action->getCenter())->toBe('52.1,4.2');
});

it('builds the center from a Coordinates option', function (): void {
    $action = DisplayMapAction::make(['center' => new Coordinates(52.1, 4.2)]);

    expect($action->getCenter())->toBe('52.1,4.2');
});

it('builds the zoom from options', function (): void {
    $action = DisplayMapAction::make(['zoom' => 12]);

    expect($action->getZoom())->toBe(12);
});

it('accepts a zoom on the edge of the range', function (int $zoom): void {
    expect((new DisplayMapAction)->setZoom($zoom)->getZoom())->toBe($zoom);
})->with([0, 21]);

it('rejects a zoom outside the 0 to 21 range', function (int $zoom): void {
    (new DisplayMapAction)->setZoom($zoom);
})->with([-1, 22])->throws(InvalidOption::class);

it('reports the accepted zoom range', function (): void {
    (new DisplayMapAction)->setZoom(22);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'zoom'. Expected from 0 to 21. Received '22'."
);

it('stores the base map', function (): void {
    $action = (new DisplayMapAction)->setBaseMap(BaseMap::Satellite);

    expect($action->getBaseMap())->toBe(BaseMap::Satellite);
});

it('stores the layer', function (): void {
    $action = (new DisplayMapAction)->setLayer(Layer::Transit);

    expect($action->getLayer())->toBe(Layer::Transit);
});

it('resolves enums from strings', function (): void {
    $action = DisplayMapAction::make([
        'basemap' => 'satellite',
        'layer' => 'transit',
    ]);

    expect($action->getBaseMap())->toBe(BaseMap::Satellite)
        ->and($action->getLayer())->toBe(Layer::Transit);
});

it('accepts enum instances', function (): void {
    $action = DisplayMapAction::make([
        'basemap' => BaseMap::Terrain,
        'layer' => Layer::None,
    ]);

    expect($action->getBaseMap())->toBe(BaseMap::Terrain)
        ->and($action->getLayer())->toBe(Layer::None);
});

it('rejects an unsupported base map', function (): void {
    DisplayMapAction::make(['basemap' => 'unsupported']);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'basemap'. Expected one of 'roadmap', 'satellite', 'terrain'. Received 'unsupported'."
);

it('rejects an unsupported layer', function (): void {
    DisplayMapAction::make(['layer' => 'unsupported']);
})->throws(InvalidOption::class);

it('rejects a base map that is neither a string nor an enum instance', function (): void {
    DisplayMapAction::make(['basemap' => 22]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'basemap'. Expected one of 'roadmap', 'satellite', 'terrain'. Received 22."
);

it('rejects a layer that is neither a string nor an enum instance', function (): void {
    DisplayMapAction::make(['layer' => ['transit']]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'layer'. Expected one of 'none', 'transit', 'traffic', 'bicycling'. Received array."
);
