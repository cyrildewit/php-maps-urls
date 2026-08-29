<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\Enums\DirectionAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;

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
        ->setWaypointPlaceIds(['abcdefghijklmnopqrstuvwxyz', 'abcdefghijklmnopqrstuvwxyz']);

    expect($action->getParameters())->toBe([
        'origin' => 'Amsterdam',
        'origin_place_id' => 'abcdefghijklmnopqrstuvwxyz',
        'destination' => 'Monnickendam',
        'destination_place_id' => 'abcdefghijklmnopqrstuvwxyz',
        'travelmode' => 'walking',
        'dir_action' => 'navigate',
        'waypoints' => 'Rotterdam|Utrecht',
        'waypoint_place_ids' => 'abcdefghijklmnopqrstuvwxyz|abcdefghijklmnopqrstuvwxyz',
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
    ]);
});

it('leaves waypoints out when the list is empty', function (): void {
    $action = (new DirectionsAction)
        ->setWaypoints([])
        ->setWaypointPlaceIds([]);

    expect($action->getParameters())
        ->toMatchArray(['waypoints' => null, 'waypoint_place_ids' => null]);
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

it('stores the travel mode', function (): void {
    $action = (new DirectionsAction)->setTravelMode(TravelMode::Driving);

    expect($action->getTravelMode())->toBe(TravelMode::Driving);
});

it('stores the direction action', function (): void {
    $action = (new DirectionsAction)->setDirectionAction(DirectionAction::Navigate);

    expect($action->getDirectionAction())->toBe(DirectionAction::Navigate);
});

it('resolves enums from strings regardless of casing', function (): void {
    $action = DirectionsAction::make([
        'travelmode' => 'WALKING',
        'dir_action' => 'navigate',
    ]);

    expect($action->getTravelMode())->toBe(TravelMode::Walking)
        ->and($action->getDirectionAction())->toBe(DirectionAction::Navigate);
});

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
    "Invalid value provided for 'travelmode'. Expected one of 'driving', 'walking', 'bicycling', 'transit'. Received 'unsupported'."
);

it('rejects an unsupported direction action', function (): void {
    DirectionsAction::make(['dir_action' => 'unsupported']);
})->throws(InvalidOption::class);
