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

use CyrildeWit\MapsUrls\Actions\DisplayMapAction;
use Exception;
use PHPUnit\Framework\TestCase;

class DisplayMapActionTest extends TestCase
{
    public function testGetParameters()
    {
        $action = (new DisplayMapAction())
            ->setCenter(40, 40)
            ->setZoom(20)
            ->setBasemap('traffic')
            ->setLayer('bicycling');

        $this->assertEquals([
            'map_action' => 'map',
            'center' => '40,40',
            'zoom' => 20,
            'basemap' => 'traffic',
            'layer' => 'bicycling',
        ], $action->getParameters());
    }

    public function testSetCenter()
    {
        $action = (new DisplayMapAction())->setCenter(20, 40);

        $this->assertEquals('20,40', $action->getCenter());
    }

    public function testSetBasemap()
    {
        $action = (new DisplayMapAction())->setBasemap('traffic');

        $this->assertEquals('traffic', $action->getBasemap());
    }

    public function testSetBasemapNone()
    {
        $action = (new DisplayMapAction())->setBasemap('none');

        $this->assertNull($action->getBasemap());
    }

    public function testSetBasemapInvalid()
    {
        $this->expectException(Exception::class);

        $action = (new DisplayMapAction())->setBasemap('unsupported');
    }

    public function testSetLayer()
    {
        $action = (new DisplayMapAction())->setLayer('transit');

        $this->assertEquals('transit', $action->getLayer());
    }

    public function testSetLayerNone()
    {
        $action = (new DisplayMapAction())->setLayer('none');

        $this->assertNull($action->getLayer());
    }

    public function testSetLayerInvalid()
    {
        $this->expectException(Exception::class);

        $action = (new DisplayMapAction())->setLayer('unsupported');
    }
}
