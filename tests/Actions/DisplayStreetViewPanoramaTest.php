<?php

namespace CyrildeWit\MapsUrls\Tests\Actions;

use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;
use CyrildeWit\MapsUrls\Exceptions\InvalidFov;
use CyrildeWit\MapsUrls\Exceptions\InvalidHeading;
use CyrildeWit\MapsUrls\Exceptions\InvalidPitch;
use PHPUnit\Framework\TestCase;

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
        $this->expectException(InvalidHeading::class);

        (new DisplayStreetViewPanoramaAction())->setHeading(-200);
    }

    public function testSetPitch()
    {
        $action = (new DisplayStreetViewPanoramaAction())->setPitch(20);

        $this->assertEquals(20, $action->getPitch());
    }

    public function testSetPitchInvalid()
    {
        $this->expectException(InvalidPitch::class);

        (new DisplayStreetViewPanoramaAction())->setPitch(120);
    }

    public function testSetFov()
    {
        $action = (new DisplayStreetViewPanoramaAction())->setFov(40);

        $this->assertEquals(40, $action->getFov());
    }

    public function testSetFovInvalid()
    {
        $this->expectException(InvalidFov::class);

        (new DisplayStreetViewPanoramaAction())->setFov(120);
    }
}
