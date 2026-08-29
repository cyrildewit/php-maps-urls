<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\Enums\Avoid;
use CyrildeWit\MapsUrls\Enums\DirectionAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
use CyrildeWit\MapsUrls\ValueObjects\Coordinates;

it('exposes the directions endpoint', function (): void {
    expect((new DirectionsAction)->getEndpoint())->toBe(DirectionsAction::ENDPOINT);
});

it('builds the query parameters', function (): void {
    $action = (new DirectionsAction)
        ->setOrigin('Amsterdam')
        ->setOriginPlaceId('abcdefghijklmnopqrstuvwxyz')
        ->setDestination('Monnickendam')
        ->setDestinationPlaceId('abcdefghijklmnopqrstuvwxyz')
        ->setTravelMode(TravelMode::Walking)
        ->setDirectionAction(DirectionAction::Navigate)
        ->setWaypoints(['Rotterdam', 'Utrecht'])
        ->setWaypointPlaceIds(['abcdefghijklmnopqrstuvwxyz', 'abcdefghijklmnopqrstuvwxyz'])
        ->setAvoid(Avoid::Tolls, Avoid::Ferries);

    expect($action->getParameters())->toBe([
        'origin' => 'Amsterdam',
        'origin_place_id' => 'abcdefghijklmnopqrstuvwxyz',
        'destination' => 'Monnickendam',
        'destination_place_id' => 'abcdefghijklmnopqrstuvwxyz',
        'travelmode' => 'walking',
        'dir_action' => 'navigate',
        'waypoints' => 'Rotterdam|Utrecht',
        'waypoint_place_ids' => 'abcdefghijklmnopqrstuvwxyz|abcdefghijklmnopqrstuvwxyz',
        'avoid' => 'tolls,ferries',
    ]);
});

it('builds null parameters when nothing is set', function (): void {
    expect((new DirectionsAction)->getParameters())->toBe([
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

it('leaves waypoints out when the list is empty', function (): void {
    $action = (new DirectionsAction)
        ->setWaypoints([])
        ->setWaypointPlaceIds([]);

    expect($action->getParameters())
        ->toMatchArray(['waypoints' => null, 'waypoint_place_ids' => null]);
});

it('leaves avoid out when the list is empty', function (): void {
    $action = (new DirectionsAction)->setAvoid();

    expect($action->getParameters())->toMatchArray(['avoid' => null]);
});

it('builds the plain options from strings and arrays', function (): void {
    $action = DirectionsAction::make([
        'origin' => 'Amsterdam',
        'origin_place_id' => 'abcdefghijklmnopqrstuvwxyz',
        'destination' => 'Monnickendam',
        'destination_place_id' => 'zyxwvutsrqponmlkjihgfedcba',
        'waypoints' => ['Rotterdam', 'Utrecht'],
        'waypoint_place_ids' => ['abc', 'def'],
    ]);

    expect($action->getOrigin())->toBe('Amsterdam')
        ->and($action->getOriginPlaceId())->toBe('abcdefghijklmnopqrstuvwxyz')
        ->and($action->getDestination())->toBe('Monnickendam')
        ->and($action->getDestinationPlaceId())->toBe('zyxwvutsrqponmlkjihgfedcba')
        ->and($action->getWaypoints())->toBe(['Rotterdam', 'Utrecht'])
        ->and($action->getWaypointPlaceIds())->toBe(['abc', 'def']);
});

it('accepts coordinates for the origin and the destination', function (): void {
    $action = (new DirectionsAction)
        ->setOrigin(new Coordinates(52.3676, 4.9041))
        ->setDestination(new Coordinates(52.4584, 5.0186));

    expect($action->getOrigin())->toBe('52.3676,4.9041')
        ->and($action->getDestination())->toBe('52.4584,5.0186');
});

it('accepts a waypoint list that mixes strings and coordinates', function (): void {
    $action = (new DirectionsAction)
        ->setWaypoints(['Rotterdam', new Coordinates(52.0907, 5.1214)]);

    expect($action->getWaypoints())->toBe(['Rotterdam', '52.0907,5.1214'])
        ->and($action->getParameters()['waypoints'])->toBe('Rotterdam|52.0907,5.1214');
});

it('builds coordinate options', function (): void {
    $action = DirectionsAction::make([
        'origin' => new Coordinates(52.3676, 4.9041),
        'destination' => new Coordinates(52.4584, 5.0186),
        'waypoints' => [new Coordinates(52.0907, 5.1214)],
    ]);

    expect($action->getOrigin())->toBe('52.3676,4.9041')
        ->and($action->getDestination())->toBe('52.4584,5.0186')
        ->and($action->getWaypoints())->toBe(['52.0907,5.1214']);
});

it('stores the travel mode', function (): void {
    $action = (new DirectionsAction)->setTravelMode(TravelMode::Driving);

    expect($action->getTravelMode())->toBe(TravelMode::Driving);
});

it('keeps the hyphen in the two-wheeler travel mode', function (): void {
    $action = DirectionsAction::make(['travelmode' => 'two-wheeler']);

    expect($action->getTravelMode())->toBe(TravelMode::TwoWheeler)
        ->and($action->getParameters()['travelmode'])->toBe('two-wheeler');
});

it('stores the features to avoid', function (): void {
    $action = (new DirectionsAction)->setAvoid(Avoid::Tolls);

    expect($action->getAvoid())->toBe([Avoid::Tolls])
        ->and($action->hasAvoid())->toBeTrue();
});

it('reports no features to avoid when none are set', function (): void {
    expect((new DirectionsAction)->hasAvoid())->toBeFalse();
});

it('separates the features to avoid with commas', function (): void {
    $action = (new DirectionsAction)->setAvoid(Avoid::Ferries, Avoid::Highways, Avoid::Tolls);

    expect($action->getParameters()['avoid'])->toBe('ferries,highways,tolls');
});

it('resolves a list of enums from strings', function (): void {
    $action = DirectionsAction::make(['avoid' => ['tolls', 'ferries']]);

    expect($action->getAvoid())->toBe([Avoid::Tolls, Avoid::Ferries]);
});

it('resolves a list that mixes strings and enum instances', function (): void {
    $action = DirectionsAction::make(['avoid' => ['tolls', Avoid::Ferries]]);

    expect($action->getAvoid())->toBe([Avoid::Tolls, Avoid::Ferries]);
});

it('wraps a single value in a list', function (): void {
    expect(DirectionsAction::make(['avoid' => 'tolls'])->getAvoid())->toBe([Avoid::Tolls])
        ->and(DirectionsAction::make(['avoid' => Avoid::Tolls])->getAvoid())->toBe([Avoid::Tolls]);
});

it('rejects an unsupported value inside a list', function (): void {
    DirectionsAction::make(['avoid' => ['tolls', 'trains']]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'avoid'. Expected one of 'ferries', 'highways', 'tolls'. Received 'trains'."
);

it('stores the direction action', function (): void {
    $action = (new DirectionsAction)->setDirectionAction(DirectionAction::Navigate);

    expect($action->getDirectionAction())->toBe(DirectionAction::Navigate);
});

it('resolves enums from strings', function (): void {
    $action = DirectionsAction::make([
        'travelmode' => 'walking',
        'dir_action' => 'navigate',
    ]);

    expect($action->getTravelMode())->toBe(TravelMode::Walking)
        ->and($action->getDirectionAction())->toBe(DirectionAction::Navigate);
});

it('rejects a string whose casing does not match the backing value', function (): void {
    DirectionsAction::make(['travelmode' => 'WALKING']);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'travelmode'. Expected one of 'driving', 'walking', 'bicycling', 'two-wheeler', 'transit'. Received 'WALKING'."
);

it('accepts enum instances', function (): void {
    $action = DirectionsAction::make([
        'travelmode' => TravelMode::Transit,
        'dir_action' => DirectionAction::Navigate,
    ]);

    expect($action->getTravelMode())->toBe(TravelMode::Transit)
        ->and($action->getDirectionAction())->toBe(DirectionAction::Navigate);
});

it('rejects an unsupported travel mode', function (): void {
    DirectionsAction::make(['travelmode' => 'unsupported']);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'travelmode'. Expected one of 'driving', 'walking', 'bicycling', 'two-wheeler', 'transit'. Received 'unsupported'."
);

it('rejects an unsupported direction action', function (): void {
    DirectionsAction::make(['dir_action' => 'unsupported']);
})->throws(InvalidOption::class);

it('rejects a travel mode that is neither a string nor an enum instance', function (): void {
    DirectionsAction::make(['travelmode' => 22]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'travelmode'. Expected one of 'driving', 'walking', 'bicycling', 'two-wheeler', 'transit'. Received 22."
);

it('rejects an instance of the wrong enum', function (): void {
    DirectionsAction::make(['travelmode' => DirectionAction::Navigate]);
})->throws(
    InvalidOption::class,
    "Invalid value provided for 'travelmode'. Expected one of 'driving', 'walking', 'bicycling', 'two-wheeler', 'transit'. Received ".DirectionAction::class.'.'
);
