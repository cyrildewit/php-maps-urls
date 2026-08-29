<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Actions\Directions;
use CyrildeWit\MapsUrls\Actions\Search;
use CyrildeWit\MapsUrls\Enums\TravelMode;
use CyrildeWit\MapsUrls\MapsUrl;

it('builds a url without any tracking parameters', function (): void {
    expect(MapsUrl::for(new Search(query: 'Rijksmuseum')))
        ->toBe('https://www.google.com/maps/search/?api=1&query=Rijksmuseum');
});

it('builds a url for any action', function (): void {
    $action = new Directions(
        origin: 'Amsterdam',
        destination: 'Utrecht',
        travelMode: TravelMode::Bicycling,
    );

    expect(MapsUrl::for($action))
        ->toBe('https://www.google.com/maps/dir/?api=1&origin=Amsterdam&destination=Utrecht&travelmode=bicycling');
});
