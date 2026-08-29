<?php

declare(strict_types=1);

use CyrildeWit\MapsUrls\Action;
use CyrildeWit\MapsUrls\Actions\Search;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
use CyrildeWit\MapsUrls\UrlGenerator;

/**
 * @param  array<string, string|int|null>  $parameters
 */
function fakeAction(string $endpoint, array $parameters = []): Action
{
    return new readonly class($endpoint, $parameters) implements Action
    {
        /**
         * @param  array<string, string|int|null>  $parameters
         */
        public function __construct(
            private string $endpoint,
            private array $parameters,
        ) {}

        public function endpoint(): string
        {
            return $this->endpoint;
        }

        public function parameters(): array
        {
            return $this->parameters;
        }
    };
}

it('builds a url from the endpoint and parameters of its action', function (): void {
    $url = new UrlGenerator()->generate(fakeAction('search/', [
        'test' => 'test',
        'foo' => 'bar',
    ]));

    expect($url)->toBe('https://www.google.com/maps/search/?api=1&test=test&foo=bar');
});

it('leaves null parameters out of the query string', function (): void {
    $url = new UrlGenerator()->generate(fakeAction('search/', [
        'query' => 'Eindhoven',
        'query_place_id' => null,
    ]));

    expect($url)->toBe('https://www.google.com/maps/search/?api=1&query=Eindhoven');
});

it('keeps parameters whose value is zero or an empty string', function (): void {
    $url = new UrlGenerator()->generate(fakeAction('endpoint/', [
        'zoom' => 0,
        'heading' => 0,
        'query' => '',
    ]));

    expect($url)->toBe('https://www.google.com/maps/endpoint/?api=1&zoom=0&heading=0&query=');
});

it('leaves out the tracking parameters that were never set', function (): void {
    $generator = new UrlGenerator;

    expect($generator->utmSource)->toBeNull()
        ->and($generator->utmCampaign)->toBeNull()
        ->and($generator->generate(new Search(query: 'Eindhoven')))
        ->toBe('https://www.google.com/maps/search/?api=1&query=Eindhoven');
});

it('appends the tracking parameters after the parameters of the action', function (): void {
    $url = new UrlGenerator(utmSource: 'my_app', utmCampaign: 'directions_request')
        ->generate(fakeAction('dir/', [
            'origin' => 'Eindhoven',
            'destination' => 'Utrecht',
        ]));

    expect($url)->toBe('https://www.google.com/maps/dir/?api=1&origin=Eindhoven&destination=Utrecht&utm_source=my_app&utm_campaign=directions_request');
});

it('sends one tracking parameter without the other', function (): void {
    expect(new UrlGenerator(utmSource: 'my_app')->generate(new Search(query: 'Eindhoven')))
        ->toBe('https://www.google.com/maps/search/?api=1&query=Eindhoven&utm_source=my_app')
        ->and(new UrlGenerator(utmCampaign: 'search_request')->generate(new Search(query: 'Eindhoven')))
        ->toBe('https://www.google.com/maps/search/?api=1&query=Eindhoven&utm_campaign=search_request');
});

it('takes the campaign for one link over the default', function (): void {
    $generator = new UrlGenerator(utmSource: 'my_app', utmCampaign: 'search_request');

    expect($generator->generate(new Search(query: 'Eindhoven'), utmCampaign: 'directions_request'))
        ->toBe('https://www.google.com/maps/search/?api=1&query=Eindhoven&utm_source=my_app&utm_campaign=directions_request');
});

it('rejects an action that writes a parameter the generator owns', function (string $queryParameter): void {
    new UrlGenerator()->generate(fakeAction('search/', [$queryParameter => 'oops']));
})->with(['api', 'utm_source', 'utm_campaign'])
    ->throws(InvalidOption::class);

it('names every parameter the action should not have written', function (): void {
    $action = fakeAction('search/', [
        'api' => 'oops',
        'query' => 'Eindhoven',
        'utm_source' => 'nope',
    ]);

    $message = null;

    try {
        new UrlGenerator()->generate($action);
    } catch (InvalidOption $invalidOption) {
        $message = $invalidOption->getMessage();
    }

    expect($message)->toContain("'api', 'utm_source'")
        ->and($message)->not->toContain('query');
});

it('serves several actions from one generator', function (): void {
    $generator = new UrlGenerator(utmSource: 'my_app');

    expect($generator->generate(new Search(query: 'Eindhoven')))
        ->toBe('https://www.google.com/maps/search/?api=1&query=Eindhoven&utm_source=my_app')
        ->and($generator->generate(fakeAction('dir/', ['origin' => 'Eindhoven'])))
        ->toBe('https://www.google.com/maps/dir/?api=1&origin=Eindhoven&utm_source=my_app');
});
