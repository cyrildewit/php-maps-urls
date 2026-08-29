<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Actions\Search;
use CyrildeWit\MapsUrls\Coordinates;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;

it('writes the search endpoint', function (): void {
    expect(new Search(query: 'Eindhoven')->endpoint())->toBe('search/');
});

it('leaves out the place id when it was never given', function (): void {
    expect(new Search(query: 'Eindhoven')->parameters())->toBe([
        'query' => 'Eindhoven',
        'query_place_id' => null,
    ]);
});

it('rejects a search without a query', function (): void {
    Search::fromArray(['query_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM']);
})->throws(
    InvalidOption::class,
    "Missing option 'query'. Google requires it."
);

it('writes the query and the place id', function (): void {
    $action = new Search(
        query: 'Eindhoven, Nederland',
        queryPlaceId: 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
    );

    expect($action->parameters())->toBe([
        'query' => 'Eindhoven, Nederland',
        'query_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
    ]);
});

it('formats a query given as coordinates', function (): void {
    $action = new Search(query: new Coordinates(47.5951518, -122.3316393));

    expect($action->parameters()['query'])->toBe('47.5951518,-122.3316393');
});

it('keeps the coordinates it was given', function (): void {
    $query = new Coordinates(47.5951518, -122.3316393);

    expect(new Search(query: $query)->query)->toBe($query);
});

it('builds from an array', function (): void {
    $action = Search::fromArray([
        'query' => 'Eindhoven, Nederland',
        'query_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
    ]);

    expect($action->query)->toBe('Eindhoven, Nederland')
        ->and($action->queryPlaceId)->toBe('ChIJn8N5VRvZxkcRmLlkgWTSmvM');
});

it('builds from an array holding a coordinate pair', function (): void {
    $action = Search::fromArray(['query' => [47.5951518, -122.3316393]]);

    expect($action->parameters()['query'])->toBe('47.5951518,-122.3316393');
});

it('rejects an option it does not support', function (): void {
    Search::fromArray(['nope' => 'value']);
})->throws(
    InvalidOption::class,
    "Unknown option 'nope'. Expected one of 'query', 'query_place_id'."
);

it('rejects an option given without a name', function (): void {
    Search::fromArray(['Eindhoven']);
})->throws(
    InvalidOption::class,
    "Unknown option '0'. Expected one of 'query', 'query_place_id'."
);

it('rejects a query that is neither a place name nor a position', function (): void {
    Search::fromArray(['query' => 123]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'query'. Expected a place name, a Coordinates instance or a [latitude, longitude] pair. Received 123."
);
