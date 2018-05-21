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

use PHPUnit\Framework\TestCase;
use CyrildeWit\MapsUrls\Actions\SearchAction;

class SearchActionTest extends TestCase
{
    public function testGetParameters()
    {
        $action = (new SearchAction())
            ->setQuery('Nederland Amsterdam')
            ->setQueryPlaceId('abcdefghijklmnopqrstuvwxyz');

        $this->assertEquals([
            'query' => 'Nederland Amsterdam',
            'query_place_id' => 'abcdefghijklmnopqrstuvwxyz',
        ], $action->getParameters());
    }

    public function testSetCoordinates()
    {
        $action = (new SearchAction())->setCoordinates(41, 2);

        $this->assertEquals('41,2', $action->getQuery());
    }
}
