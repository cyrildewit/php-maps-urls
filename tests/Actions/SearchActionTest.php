<?php

namespace CyrildeWit\MapsUrls\Tests\Actions;

use CyrildeWit\MapsUrls\Actions\SearchAction;
use PHPUnit\Framework\TestCase;

class SearchActionTest extends TestCase
{
    public function testGetParameters()
    {
        $action = (new SearchAction())
            ->setQuery('Eindhoven, Nederland')
            ->setQueryPlaceId('ChIJn8N5VRvZxkcRmLlkgWTSmvM');

        $this->assertEquals([
            'query' => 'Eindhoven, Nederland',
            'query_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
        ], $action->getParameters());
    }

    public function testSetQueryCoordinates()
    {
        $action = (new SearchAction())->setQueryCoordinates(41, 2);

        $this->assertEquals('41,2', $action->getQuery());
    }
}
