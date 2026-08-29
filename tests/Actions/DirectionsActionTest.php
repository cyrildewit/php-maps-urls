<?php

namespace CyrildeWit\MapsUrls\Tests\Actions;

use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\Enums\DirectionAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
use PHPUnit\Framework\TestCase;

class DirectionsActionTest extends TestCase
{
    public function testGetEndpoint()
    {
        $action = new DirectionsAction();

        $this->assertEquals(DirectionsAction::ENDPOINT, $action->getEndpoint());
    }

    public function testGetParameters()
    {
        $action = (new DirectionsAction())
            ->setOrigin('Amsterdam')
            ->setOriginPlaceId('abcdefghijklmnopqrstuvwxyz')
            ->setDestination('Monnickendam')
            ->setDestinationPlaceId('abcdefghijklmnopqrstuvwxyz')
            ->setTravelMode(TravelMode::Walking)
            ->setDirectionAction(DirectionAction::Navigate)
            ->setWaypoints(['Rotterdam', 'Utrecht'])
            ->setWaypointPlaceIds(['abcdefghijklmnopqrstuvwxyz', 'abcdefghijklmnopqrstuvwxyz']);

        $this->assertEquals([
            'origin' => 'Amsterdam',
            'origin_place_id' => 'abcdefghijklmnopqrstuvwxyz',
            'destination' => 'Monnickendam',
            'destination_place_id' => 'abcdefghijklmnopqrstuvwxyz',
            'travelmode' => 'walking',
            'dir_action' => 'navigate',
            'waypoints' => 'Rotterdam|Utrecht',
            'waypoint_place_ids' => 'abcdefghijklmnopqrstuvwxyz|abcdefghijklmnopqrstuvwxyz',
        ], $action->getParameters());
    }

    public function testSetTravelMode()
    {
        $action = (new DirectionsAction())->setTravelMode(TravelMode::Driving);

        $this->assertEquals(TravelMode::Driving, $action->getTravelMode());
    }

    public function testSetDirectionAction()
    {
        $action = (new DirectionsAction())->setDirectionAction(DirectionAction::Navigate);

        $this->assertEquals(DirectionAction::Navigate, $action->getDirectionAction());
    }

    public function testMakeResolvesEnumsFromStrings()
    {
        $action = DirectionsAction::make([
            'travelmode' => 'WALKING',
            'dir_action' => 'navigate',
        ]);

        $this->assertEquals(TravelMode::Walking, $action->getTravelMode());
        $this->assertEquals(DirectionAction::Navigate, $action->getDirectionAction());
    }

    public function testMakeAcceptsEnumInstances()
    {
        $action = DirectionsAction::make([
            'travelmode' => TravelMode::Transit,
            'dir_action' => DirectionAction::Navigate,
        ]);

        $this->assertEquals(TravelMode::Transit, $action->getTravelMode());
        $this->assertEquals(DirectionAction::Navigate, $action->getDirectionAction());
    }

    public function testMakeThrowsOnUnsupportedTravelMode()
    {
        $this->expectException(InvalidOption::class);
        $this->expectExceptionMessage("Invalid value provided for 'travelmode'. Expected one of 'driving', 'walking', 'bicycling', 'transit'. Received 'unsupported'.");

        DirectionsAction::make(['travelmode' => 'unsupported']);
    }

    public function testMakeThrowsOnUnsupportedDirectionAction()
    {
        $this->expectException(InvalidOption::class);

        DirectionsAction::make(['dir_action' => 'unsupported']);
    }
}
