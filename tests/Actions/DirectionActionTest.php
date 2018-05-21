<?php

namespace CyrildeWit\MapsUrls\Tests\Actions;

/*
 * This file is part of the Maps URLs package.
 *
 * (c) Cyril de Wit <github@cyrildewit.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Exception;
use PHPUnit\Framework\TestCase;
use CyrildeWit\MapsUrls\Actions\DirectionAction;

class DirectionActionTest extends TestCase
{
    public function testGetParameters()
    {
        $action = (new DirectionAction())
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

    public function testSetTravelmode()
    {
        $action = (new DirectionAction())->setTravelmode('driving');

        $this->assertEquals('driving', $action->getTravelmode());
    }

    public function testSetTravelmodeReturnsException()
    {
        $this->expectException(Exception::class);

        $action = (new DirectionAction())->setTravelmode('unsupported');
    }

    public function testSetDirectionAction()
    {
        $action = (new DirectionAction())->setDirectionAction('navigate');

        $this->assertEquals('navigate', $action->getDirectionAction());
    }

    public function testSetDirectionActionReturnsException()
    {
        $this->expectException(Exception::class);

        $action = (new DirectionAction())->setDirectionAction('unsupported');
    }
}
