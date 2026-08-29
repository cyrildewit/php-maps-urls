<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Actions\AbstractAction;
use CyrildeWit\MapsUrls\UrlGenerator;

function fakeAction(string $endpoint, array $parameters = []): AbstractAction
{
    return new class($endpoint, $parameters) extends AbstractAction
    {
        public function __construct(
            private readonly string $endpoint,
            private readonly array $parameters,
        ) {}

        public function getEndpoint(): string
        {
            return $this->endpoint;
        }

        public function getParameters(): array
        {
            return $this->parameters;
        }
    };
}

it('builds a url from the endpoint and parameters of its action', function (): void {
    $urlGenerator = new UrlGenerator(fakeAction('search/', [
        'test' => 'test',
        'foo' => 'bar',
    ]));

    expect($urlGenerator->generate())
        ->toBe('https://www.google.com/maps/search/?api=1&test=test&foo=bar');
});

it('leaves null parameters out of the query string', function (): void {
    $urlGenerator = new UrlGenerator(fakeAction('search/', [
        'query' => 'Eindhoven',
        'query_place_id' => null,
    ]));

    expect($urlGenerator->generate())
        ->toBe('https://www.google.com/maps/search/?api=1&query=Eindhoven');
});

it('generates from the new action after it is swapped', function (): void {
    $urlGenerator = new UrlGenerator(fakeAction('endpoint/', ['test' => 'before']));

    expect($urlGenerator->generate())
        ->toBe('https://www.google.com/maps/endpoint/?api=1&test=before');

    $urlGenerator->setAction(fakeAction('endpoint/', ['test' => 'after']));

    expect($urlGenerator->generate())
        ->toBe('https://www.google.com/maps/endpoint/?api=1&test=after');
});
