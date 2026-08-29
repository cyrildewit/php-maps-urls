<?php

use CyrildeWit\MapsUrls\Actions\SearchAction;

it('exposes the search endpoint', function () {
    expect((new SearchAction())->getEndpoint())->toBe(SearchAction::ENDPOINT);
});

it('builds the query parameters', function () {
    $action = (new SearchAction())
        ->setQuery('Eindhoven, Nederland')
        ->setQueryPlaceId('ChIJn8N5VRvZxkcRmLlkgWTSmvM');

    expect($action->getParameters())->toBe([
        'query' => 'Eindhoven, Nederland',
        'query_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
    ]);
});

it('formats query coordinates as a comma separated pair', function () {
    $action = (new SearchAction())->setQueryCoordinates(41, 2);

    expect($action->getQuery())->toBe('41,2');
});

it('builds null parameters when nothing is set', function () {
    expect((new SearchAction())->getParameters())->toBe([
        'query' => null,
        'query_place_id' => null,
    ]);
});

it('builds from options', function () {
    $action = SearchAction::make([
        'query' => 'Eindhoven, Nederland',
        'query_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
    ]);

    expect($action->getQuery())->toBe('Eindhoven, Nederland')
        ->and($action->getQueryPlaceId())->toBe('ChIJn8N5VRvZxkcRmLlkgWTSmvM');
});

it('builds query coordinates from options', function () {
    $action = SearchAction::make(['query_coordinates' => [41, 2]]);

    expect($action->getQuery())->toBe('41,2');
});
