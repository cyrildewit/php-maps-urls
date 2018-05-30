<?php

namespace CyrildeWit\MapsUrls\Tests;

/*
 * This file is part of the Maps URLs package.
 *
 * (c) Cyril de Wit <github@cyrildewit.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use CyrildeWit\MapsUrls\UrlGenerator;
use CyrildeWit\MapsUrls\Actions\AbstractAction;

class UrlGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $urlGenerator = new UrlGenerator(new class extends AbstractAction {
            public function getEndpoint(): string
            {
                return 'search/';
            }

            public function getParameters(): array
            {
                return [
                    'test' => 'test',
                    'foo' => 'bar',
                ];
            }
        });

        $this->assertEquals(
            'https://www.google.com/maps/search/?api=1&test=test&foo=bar',
            $urlGenerator->generate()
        );
    }
}
