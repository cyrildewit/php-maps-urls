<?php

namespace CyrildeWit\MapsUrls\Tests\Actions;

use CyrildeWit\MapsUrls\Actions\DisplayMapAction;
use CyrildeWit\MapsUrls\Enums\BaseMap;
use CyrildeWit\MapsUrls\Enums\Layer;
use CyrildeWit\MapsUrls\Exceptions\InvalidBaseMap;
use CyrildeWit\MapsUrls\Exceptions\InvalidLayer;
use PHPUnit\Framework\TestCase;

class DisplayMapActionTest extends TestCase
{
    public function testGetEndpoint()
    {
        $action = new DisplayMapAction();

        $this->assertEquals(DisplayMapAction::ENDPOINT, $action->getEndpoint());
    }

    public function testGetParameters()
    {
        $action = (new DisplayMapAction())
            ->setCenter(40, 40)
            ->setZoom(20)
            ->setBaseMap(BaseMap::TRAFFIC)
            ->setLayer(Layer::BICYCLING);

        $this->assertEquals([
            'map_action' => DisplayMapAction::MAP_ACTION,
            'center' => '40,40',
            'zoom' => '20',
            'basemap' => BaseMap::TRAFFIC,
            'layer' => Layer::BICYCLING,
        ], $action->getParameters());
    }

    public function testGetCenterReturnsNullIfIncomplete()
    {
        $action = (new DisplayMapAction())->setCenterLatitude(40);

        $this->assertNull($action->getCenter());
    }

    public function testSetCenter()
    {
        $action = (new DisplayMapAction())->setCenter(20, 40);

        $this->assertEquals('20,40', $action->getCenter());
    }

    public function testSetBasemap()
    {
        $action = (new DisplayMapAction())->setBaseMap(BaseMap::TRAFFIC);

        $this->assertEquals(BaseMap::TRAFFIC, $action->getBasemap());
    }

    public function testSetBasemapInvalid()
    {
        $this->expectException(InvalidBaseMap::class);

        (new DisplayMapAction())->setBaseMap('unsupported');
    }

    public function testSetLayer()
    {
        $action = (new DisplayMapAction())->setLayer(Layer::TRANSIT);

        $this->assertEquals(Layer::TRANSIT, $action->getLayer());
    }

    public function testSetLayerInvalid()
    {
        $this->expectException(InvalidLayer::class);

        (new DisplayMapAction())->setLayer('unsupported');
    }
}
