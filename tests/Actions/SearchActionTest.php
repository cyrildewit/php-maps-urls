<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Actions\SearchAction;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
use CyrildeWit\MapsUrls\ValueObjects\Coordinates;

it('exposes the search endpoint', function (): void {
    expect((new SearchAction)->getEndpoint())->toBe(SearchAction::ENDPOINT);
});

it('builds the query parameters', function (): void {
    $action = (new SearchAction)
        ->setQuery('Eindhoven, Nederland')
        ->setQueryPlaceId('ChIJn8N5VRvZxkcRmLlkgWTSmvM');

    expect($action->getParameters())->toBe([
        'query' => 'Eindhoven, Nederland',
        'query_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
    ]);
});

it('accepts coordinates as a query', function (): void {
    $action = (new SearchAction)->setQuery(new Coordinates(41, 2));

    expect($action->getQuery())->toBe('41,2');
});

it('builds null parameters when nothing is set', function (): void {
    expect((new SearchAction)->getParameters())->toBe([
        'query' => null,
        'query_place_id' => null,
    ]);
});

it('builds from options', function (): void {
    $action = SearchAction::make([
        'query' => 'Eindhoven, Nederland',
        'query_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
    ]);

    expect($action->getQuery())->toBe('Eindhoven, Nederland')
        ->and($action->getQueryPlaceId())->toBe('ChIJn8N5VRvZxkcRmLlkgWTSmvM');
});

it('builds a coordinate query from options', function (): void {
    $action = SearchAction::make(['query' => new Coordinates(41, 2)]);

    expect($action->getQuery())->toBe('41,2');
});

it('rejects the removed query_coordinates option', function (): void {
    SearchAction::make(['query_coordinates' => [41, 2]]);
})->throws(
    InvalidOption::class,
    "Unknown option 'query_coordinates'. Expected one of 'query', 'query_place_id'."
);
