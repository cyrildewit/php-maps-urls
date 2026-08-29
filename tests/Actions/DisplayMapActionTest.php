<?php

namespace CyrildeWit\MapsUrls\Tests\Actions;

use CyrildeWit\MapsUrls\Actions\DisplayMapAction;
use CyrildeWit\MapsUrls\Enums\BaseMap;
use CyrildeWit\MapsUrls\Enums\Layer;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
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
            ->setBaseMap(BaseMap::Traffic)
            ->setLayer(Layer::Bicycling);

        $this->assertEquals([
            'map_action' => DisplayMapAction::MAP_ACTION,
            'center' => '40,40',
            'zoom' => '20',
            'basemap' => 'traffic',
            'layer' => 'bicycling',
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

    public function testSetBaseMap()
    {
        $action = (new DisplayMapAction())->setBaseMap(BaseMap::Traffic);

        $this->assertEquals(BaseMap::Traffic, $action->getBaseMap());
    }

    public function testSetLayer()
    {
        $action = (new DisplayMapAction())->setLayer(Layer::Transit);

        $this->assertEquals(Layer::Transit, $action->getLayer());
    }

    public function testMakeResolvesEnumsFromStrings()
    {
        $action = DisplayMapAction::make([
            'basemap' => 'TRAFFIC',
            'layer' => 'transit',
        ]);

        $this->assertEquals(BaseMap::Traffic, $action->getBaseMap());
        $this->assertEquals(Layer::Transit, $action->getLayer());
    }

    public function testMakeAcceptsEnumInstances()
    {
        $action = DisplayMapAction::make([
            'basemap' => BaseMap::Bicycling,
            'layer' => Layer::None,
        ]);

        $this->assertEquals(BaseMap::Bicycling, $action->getBaseMap());
        $this->assertEquals(Layer::None, $action->getLayer());
    }

    public function testMakeThrowsOnUnsupportedBaseMap()
    {
        $this->expectException(InvalidOption::class);
        $this->expectExceptionMessage("Invalid value provided for 'basemap'. Expected one of 'none', 'traffic', 'bicycling'. Received 'unsupported'.");

        DisplayMapAction::make(['basemap' => 'unsupported']);
    }

    public function testMakeThrowsOnUnsupportedLayer()
    {
        $this->expectException(InvalidOption::class);

        DisplayMapAction::make(['layer' => 'unsupported']);
    }
}
