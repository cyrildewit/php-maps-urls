<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Actions\Directions;
use CyrildeWit\MapsUrls\Coordinates;
use CyrildeWit\MapsUrls\Enums\Avoid;
use CyrildeWit\MapsUrls\Enums\DirectionAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;

it('writes the directions endpoint', function (): void {
    expect(new Directions()->endpoint())->toBe('dir/');
});

it('leaves out the parameters that were never given', function (): void {
    expect(new Directions()->parameters())->toBe([
        'origin' => null,
        'origin_place_id' => null,
        'destination' => null,
        'destination_place_id' => null,
        'travelmode' => null,
        'dir_action' => null,
        'waypoints' => null,
        'waypoint_place_ids' => null,
        'avoid' => null,
    ]);
});

it('writes every parameter it was given', function (): void {
    $action = new Directions(
        origin: 'Amsterdam',
        originPlaceId: 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
        destination: 'Utrecht',
        destinationPlaceId: 'ChIJTZfQeLgFxkcRQhAYGf9HbrU',
        travelMode: TravelMode::Bicycling,
        directionAction: DirectionAction::Navigate,
        waypoints: ['Rotterdam', 'Gouda'],
        waypointPlaceIds: ['ChIJAVkDPzdOqEcRcDteW0YgIQQ', 'ChIJD7fiBh9u5kcRYJSMaMOCCwQ'],
        avoid: [Avoid::Tolls, Avoid::Ferries],
    );

    expect($action->parameters())->toBe([
        'origin' => 'Amsterdam',
        'origin_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
        'destination' => 'Utrecht',
        'destination_place_id' => 'ChIJTZfQeLgFxkcRQhAYGf9HbrU',
        'travelmode' => 'bicycling',
        'dir_action' => 'navigate',
        'waypoints' => 'Rotterdam|Gouda',
        'waypoint_place_ids' => 'ChIJAVkDPzdOqEcRcDteW0YgIQQ|ChIJD7fiBh9u5kcRYJSMaMOCCwQ',
        'avoid' => 'tolls,ferries',
    ]);
});

it('formats an origin and a destination given as coordinates', function (): void {
    $action = new Directions(
        origin: new Coordinates(52.3676, 4.9041),
        destination: new Coordinates(52.0907, 5.1214),
    );

    expect($action->parameters()['origin'])->toBe('52.3676,4.9041')
        ->and($action->parameters()['destination'])->toBe('52.0907,5.1214');
});

it('mixes place names and coordinates in the waypoints', function (): void {
    $action = new Directions(waypoints: ['Berlin,Germany', new Coordinates(48.8566, 2.3522)]);

    expect($action->parameters()['waypoints'])->toBe('Berlin,Germany|48.8566,2.3522');
});

it('accepts waypoints without any place ids', function (): void {
    $action = new Directions(waypoints: ['Rotterdam', 'Gouda']);

    expect($action->parameters()['waypoint_place_ids'])->toBeNull();
});

it('rejects fewer place ids than waypoints', function (): void {
    new Directions(
        waypoints: ['Berlin', 'Paris', 'Lyon'],
        waypointPlaceIds: ['ChIJAVkDPzdOqEcRcDteW0YgIQQ'],
    );
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'waypoint_place_ids'. Expected one place ID for each of the 3 waypoints, or none at all. Received 1."
);

it('rejects an origin place id without an origin', function (): void {
    new Directions(originPlaceId: 'ChIJn8N5VRvZxkcRmLlkgWTSmvM');
})->throws(
    InvalidOption::class,
    "Missing option 'origin'. Google requires it alongside 'origin_place_id'."
);

it('rejects a destination place id without a destination', function (): void {
    new Directions(destinationPlaceId: 'ChIJTZfQeLgFxkcRQhAYGf9HbrU');
})->throws(
    InvalidOption::class,
    "Missing option 'destination'. Google requires it alongside 'destination_place_id'."
);

it('rejects place ids without any waypoints', function (): void {
    new Directions(waypointPlaceIds: ['ChIJAVkDPzdOqEcRcDteW0YgIQQ']);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'waypoint_place_ids'. Expected one place ID for each of the 0 waypoints, or none at all. Received 1."
);

it('builds from an array', function (): void {
    $action = Directions::fromArray([
        'origin' => 'Eindhoven, Nederland',
        'origin_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
        'destination' => 'Monnickendam, Nederland',
        'destination_place_id' => 'ChIJTZfQeLgFxkcRQhAYGf9HbrU',
        'travelmode' => TravelMode::Driving,
        'dir_action' => DirectionAction::Navigate,
        'waypoints' => ['Berlin,Germany', 'Paris,France'],
        'waypoint_place_ids' => ['ChIJAVkDPzdOqEcRcDteW0YgIQQ', 'ChIJD7fiBh9u5kcRYJSMaMOCCwQ'],
        'avoid' => [Avoid::Tolls, Avoid::Ferries],
    ]);

    expect($action->origin)->toBe('Eindhoven, Nederland')
        ->and($action->travelMode)->toBe(TravelMode::Driving)
        ->and($action->directionAction)->toBe(DirectionAction::Navigate)
        ->and($action->waypoints)->toBe(['Berlin,Germany', 'Paris,France'])
        ->and($action->avoid)->toBe([Avoid::Tolls, Avoid::Ferries]);
});

it('resolves the string behind an enum case', function (): void {
    $action = Directions::fromArray([
        'travelmode' => 'driving',
        'dir_action' => 'navigate',
        'avoid' => ['tolls'],
    ]);

    expect($action->travelMode)->toBe(TravelMode::Driving)
        ->and($action->directionAction)->toBe(DirectionAction::Navigate)
        ->and($action->avoid)->toBe([Avoid::Tolls]);
});

it('takes a single value for a parameter that holds a list', function (): void {
    $action = Directions::fromArray([
        'avoid' => 'tolls',
        'waypoints' => 'Berlin,Germany',
        'waypoint_place_ids' => 'ChIJAVkDPzdOqEcRcDteW0YgIQQ',
    ]);

    expect($action->avoid)->toBe([Avoid::Tolls])
        ->and($action->waypoints)->toBe(['Berlin,Germany'])
        ->and($action->waypointPlaceIds)->toBe(['ChIJAVkDPzdOqEcRcDteW0YgIQQ']);
});

it('takes a coordinate pair for a place', function (): void {
    $action = Directions::fromArray([
        'origin' => [52.3676, 4.9041],
        'waypoints' => [[48.8566, 2.3522], 'Berlin,Germany'],
    ]);

    expect($action->parameters()['origin'])->toBe('52.3676,4.9041')
        ->and($action->parameters()['waypoints'])->toBe('48.8566,2.3522|Berlin,Germany');
});

it('takes a coordinate pair written as numeric strings', function (): void {
    $action = Directions::fromArray(['origin' => ['52.3676', '4.9041']]);

    expect($action->parameters()['origin'])->toBe('52.3676,4.9041');
});

it('takes a Coordinates instance inside the waypoints', function (): void {
    $waypoint = new Coordinates(48.8566, 2.3522);

    expect(Directions::fromArray(['waypoints' => ['Berlin', $waypoint]])->waypoints)
        ->toBe(['Berlin', $waypoint]);
});

it('rejects a string that is not an enum case exactly', function (): void {
    Directions::fromArray(['travelmode' => 'Driving']);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'travelmode'. Expected one of 'driving', 'walking', 'bicycling', 'two-wheeler', 'transit'. Received 'Driving'."
);

it('rejects an option it does not support', function (): void {
    Directions::fromArray(['nope' => 'value']);
})->throws(InvalidOption::class);

it('takes a single Coordinates instance as the waypoints', function (): void {
    $waypoint = new Coordinates(48.8566, 2.3522);

    expect(Directions::fromArray(['waypoints' => $waypoint])->waypoints)->toBe([$waypoint]);
});

it('rejects waypoints that are not places', function (): void {
    Directions::fromArray(['waypoints' => 42]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'waypoints'. Expected a place name, a Coordinates instance or a [latitude, longitude] pair. Received 42."
);

it('rejects waypoints given with keys rather than as a list', function (): void {
    Directions::fromArray(['waypoints' => ['first' => 'Berlin']]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'waypoints'. Expected a place or a list of places. Received array."
);

it('rejects a place given as a pair that is not numeric', function (): void {
    Directions::fromArray(['origin' => ['north', 'east']]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'origin'. Expected a [latitude, longitude] pair. Received array."
);

it('rejects a pair whose latitude is not numeric', function (): void {
    Directions::fromArray(['origin' => ['north', 4.9041]]);
})->throws(InvalidOption::class);

it('rejects a pair whose longitude is not numeric', function (): void {
    Directions::fromArray(['origin' => [52.3676, 'east']]);
})->throws(InvalidOption::class);

it('rejects the things to avoid given with keys rather than as a list', function (): void {
    Directions::fromArray(['avoid' => ['first' => Avoid::Tolls]]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'avoid'. Expected a value or a list of values. Received array."
);

it('rejects place ids that are not strings', function (): void {
    Directions::fromArray(['waypoint_place_ids' => 42]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'waypoint_place_ids'. Expected a string or a list of strings. Received 42."
);

it('rejects place ids given with keys rather than as a list', function (): void {
    Directions::fromArray(['waypoint_place_ids' => ['first' => 'ChIJAVkDPzdOqEcRcDteW0YgIQQ']]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'waypoint_place_ids'. Expected a string or a list of strings. Received array."
);

it('rejects a place id inside the list that is not a string', function (): void {
    Directions::fromArray(['waypoint_place_ids' => [42]]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'waypoint_place_ids'. Expected a string or a list of strings. Received 42."
);
