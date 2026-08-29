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

it('keeps parameters whose value is zero or an empty string', function (): void {
    $urlGenerator = new UrlGenerator(fakeAction('endpoint/', [
        'zoom' => 0,
        'heading' => 0,
        'query' => '',
    ]));

    expect($urlGenerator->generate())
        ->toBe('https://www.google.com/maps/endpoint/?api=1&zoom=0&heading=0&query=');
});

it('generates from the new action after it is swapped', function (): void {
    $urlGenerator = new UrlGenerator(fakeAction('endpoint/', ['test' => 'before']));

    expect($urlGenerator->generate())
        ->toBe('https://www.google.com/maps/endpoint/?api=1&test=before');

    $urlGenerator->setAction(fakeAction('endpoint/', ['test' => 'after']));

    expect($urlGenerator->generate())
        ->toBe('https://www.google.com/maps/endpoint/?api=1&test=after');
});

it('leaves out the tracking parameters that were never set', function (): void {
    $urlGenerator = new UrlGenerator(fakeAction('search/', ['query' => 'Eindhoven']));

    expect($urlGenerator->getUtmSource())->toBeNull()
        ->and($urlGenerator->getUtmCampaign())->toBeNull()
        ->and($urlGenerator->generate())
        ->toBe('https://www.google.com/maps/search/?api=1&query=Eindhoven');
});

it('appends the tracking parameters after the parameters of the action', function (): void {
    $urlGenerator = new UrlGenerator(fakeAction('dir/', [
        'origin' => 'Eindhoven',
        'destination' => 'Utrecht',
    ]));

    $urlGenerator
        ->setUtmSource('my_app')
        ->setUtmCampaign('directions_request');

    expect($urlGenerator->getUtmSource())->toBe('my_app')
        ->and($urlGenerator->getUtmCampaign())->toBe('directions_request')
        ->and($urlGenerator->generate())
        ->toBe('https://www.google.com/maps/dir/?api=1&origin=Eindhoven&destination=Utrecht&utm_source=my_app&utm_campaign=directions_request');
});

it('sends one tracking parameter without the other', function (): void {
    $urlGenerator = new UrlGenerator(fakeAction('search/', ['query' => 'Eindhoven']));

    expect($urlGenerator->setUtmSource('my_app')->generate())
        ->toBe('https://www.google.com/maps/search/?api=1&query=Eindhoven&utm_source=my_app');

    $urlGenerator = new UrlGenerator(fakeAction('search/', ['query' => 'Eindhoven']));

    expect($urlGenerator->setUtmCampaign('search_request')->generate())
        ->toBe('https://www.google.com/maps/search/?api=1&query=Eindhoven&utm_campaign=search_request');
});

it('drops a tracking parameter that is set back to null', function (): void {
    $urlGenerator = new UrlGenerator(fakeAction('search/', ['query' => 'Eindhoven']));

    $urlGenerator
        ->setUtmSource('my_app')
        ->setUtmCampaign('search_request')
        ->setUtmCampaign(null);

    expect($urlGenerator->getUtmCampaign())->toBeNull()
        ->and($urlGenerator->generate())
        ->toBe('https://www.google.com/maps/search/?api=1&query=Eindhoven&utm_source=my_app');
});

it('keeps the tracking parameters when the action is swapped', function (): void {
    $urlGenerator = new UrlGenerator(fakeAction('search/', ['query' => 'Eindhoven']));

    $urlGenerator
        ->setUtmSource('my_app')
        ->setAction(fakeAction('dir/', ['origin' => 'Eindhoven']));

    expect($urlGenerator->generate())
        ->toBe('https://www.google.com/maps/dir/?api=1&origin=Eindhoven&utm_source=my_app');
});
