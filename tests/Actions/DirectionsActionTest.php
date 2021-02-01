<?php

namespace CyrildeWit\MapsUrls\Tests\Actions;

use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;
use CyrildeWit\MapsUrls\Exceptions\InvalidDirectionAction;
use CyrildeWit\MapsUrls\Exceptions\InvalidTravelMode;
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
            ->setTravelmode('walking')
            ->setDirectionAction('navigate')
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
        $action = (new DirectionsAction())->setTravelmode(TravelMode::DRIVING);

        $this->assertEquals(TravelMode::DRIVING, $action->getTravelmode());
    }

    public function testSetTravelModeReturnsException()
    {
        $this->expectException(InvalidTravelMode::class);

        (new DirectionsAction())->setTravelmode('unsupported');
    }

    public function testSetDirectionAction()
    {
        $action = (new DirectionsAction())->setDirectionAction('navigate');

        $this->assertEquals('navigate', $action->getDirectionAction());
    }

    public function testSetDirectionActionReturnsException()
    {
        $this->expectException(InvalidDirectionAction::class);

        (new DirectionsAction())->setDirectionAction('unsupported');
    }
}
