<?php

use CyrildeWit\MapsUrls\Actions\DisplayMapAction;
use CyrildeWit\MapsUrls\Enums\BaseMap;
use CyrildeWit\MapsUrls\Enums\Layer;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;

it('exposes the display map endpoint', function () {
    expect((new DisplayMapAction())->getEndpoint())->toBe(DisplayMapAction::ENDPOINT);
});

it('builds the query parameters', function () {
    $action = (new DisplayMapAction())
        ->setCenter(40, 40)
        ->setZoom(20)
        ->setBaseMap(BaseMap::Traffic)
        ->setLayer(Layer::Bicycling);

    expect($action->getParameters())->toBe([
        'map_action' => DisplayMapAction::MAP_ACTION,
        'center' => '40,40',
        'zoom' => 20,
        'basemap' => 'traffic',
        'layer' => 'bicycling',
    ]);
});

it('formats the center as a comma separated pair', function () {
    $action = (new DisplayMapAction())->setCenter(20, 40);

    expect($action->getCenter())->toBe('20,40');
});

it('has no center until both coordinates are set', function () {
    $action = (new DisplayMapAction())->setCenterLatitude(40);

    expect($action->getCenter())->toBeNull();
});

it('keeps a center on the equator or the prime meridian', function () {
    $action = (new DisplayMapAction())->setCenter(0, 0);

    expect($action->getCenter())->toBe('0,0');
});

it('builds null parameters when nothing is set', function () {
    expect((new DisplayMapAction())->getParameters())->toBe([
        'map_action' => DisplayMapAction::MAP_ACTION,
        'center' => null,
        'zoom' => null,
        'basemap' => null,
        'layer' => null,
    ]);
});

it('builds the center from options', function () {
    $action = DisplayMapAction::make(['center' => [52.1, 4.2]]);

    expect($action->getCenter())->toBe('52.1,4.2');
});

it('builds the zoom from options', function () {
    $action = DisplayMapAction::make(['zoom' => 12]);

    expect($action->getZoom())->toBe(12);
});

it('stores the base map', function () {
    $action = (new DisplayMapAction())->setBaseMap(BaseMap::Traffic);

    expect($action->getBaseMap())->toBe(BaseMap::Traffic);
});

it('stores the layer', function () {
    $action = (new DisplayMapAction())->setLayer(Layer::Transit);

    expect($action->getLayer())->toBe(Layer::Transit);
});

it('resolves enums from strings regardless of casing', function () {
    $action = DisplayMapAction::make([
        'basemap' => 'TRAFFIC',
        'layer' => 'transit',
    ]);

    expect($action->getBaseMap())->toBe(BaseMap::Traffic)
        ->and($action->getLayer())->toBe(Layer::Transit);
});

it('accepts enum instances', function () {
    $action = DisplayMapAction::make([
        'basemap' => BaseMap::Bicycling,
        'layer' => Layer::None,
    ]);

    expect($action->getBaseMap())->toBe(BaseMap::Bicycling)
        ->and($action->getLayer())->toBe(Layer::None);
});

it('rejects an unsupported base map', function () {
    DisplayMapAction::make(['basemap' => 'unsupported']);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'basemap'. Expected one of 'none', 'traffic', 'bicycling'. Received 'unsupported'."
);

it('rejects an unsupported layer', function () {
    DisplayMapAction::make(['layer' => 'unsupported']);
})->throws(InvalidOption::class);
