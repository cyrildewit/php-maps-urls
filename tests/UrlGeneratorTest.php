<?php

namespace CyrildeWit\MapsUrls\Tests;

use CyrildeWit\MapsUrls\Actions\AbstractAction;
use CyrildeWit\MapsUrls\UrlGenerator;
use PHPUnit\Framework\TestCase;

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

    public function testSetAction()
    {
        $urlGenerator = new UrlGenerator(new class extends AbstractAction {
            public function getEndpoint(): string
            {
                return 'endpoint/';
            }

            public function getParameters(): array
            {
                return [
                    'test' => 'before',
                ];
            }
        });

        $this->assertEquals(
            'https://www.google.com/maps/endpoint/?api=1&test=before',
            $urlGenerator->generate()
        );

        $urlGenerator->setAction(new class extends AbstractAction {
            public function getEndpoint(): string
            {
                return 'endpoint/';
            }

            public function getParameters(): array
            {
                return [
                    'test' => 'after',
                ];
            }
        });

        $this->assertEquals(
            'https://www.google.com/maps/endpoint/?api=1&test=after',
            $urlGenerator->generate()
        );
    }
}
