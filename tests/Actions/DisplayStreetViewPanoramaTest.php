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
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

class DisplayStreetViewPanoramaTest extends TestCase
{
    public function testGetParameters()
    {
        $action = (new DisplayStreetViewPanoramaAction())
            ->setViewpoint(20, 40)
            ->setPanoramaId('abcdefghijklmnopqrstuvwxyz')
            ->setHeading(100)
            ->setPitch(40)
            ->setFov(20);

        $this->assertEquals([
            'map_action' => 'pano',
            'viewpoint' => '20,40',
            'pano' => 'abcdefghijklmnopqrstuvwxyz',
            'heading' => 100,
            'pitch' => 40,
            'fov' => 20,
        ], $action->getParameters());
    }

    public function testSetViewpoint()
    {
        $action = (new DisplayStreetViewPanoramaAction())->setViewpoint(20, 40);

        $this->assertEquals('20,40', $action->getViewpoint());
    }

    public function testSetHeading()
    {
        $action = (new DisplayStreetViewPanoramaAction())->setHeading(300);

        $this->assertEquals(300, $action->getHeading());
    }

    public function testSetHeadingInvalid()
    {
        $this->expectException(Exception::class);

        $action = (new DisplayStreetViewPanoramaAction())->setHeading(-200);
    }

    public function testSetPitch()
    {
        $action = (new DisplayStreetViewPanoramaAction())->setPitch(20);

        $this->assertEquals(20, $action->getPitch());
    }

    public function testSetPitchInvalid()
    {
        $this->expectException(Exception::class);

        $action = (new DisplayStreetViewPanoramaAction())->setPitch(120);
    }

    public function testSetFov()
    {
        $action = (new DisplayStreetViewPanoramaAction())->setFov(40);

        $this->assertEquals(40, $action->getFov());
    }

    public function testSetFovInvalid()
    {
        $this->expectException(Exception::class);

        $action = (new DisplayStreetViewPanoramaAction())->setFov(120);
    }
}
