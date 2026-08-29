<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls;

use CyrildeWit\MapsUrls\Exceptions\InvalidOption;

final readonly class UrlGenerator
{
    private const string BASE_URL = 'https://www.google.com/maps/';

    private const string API_VERSION = '1';

    /**
     * The generator writes these three itself. An action returning one of them
     * would land next to the generator's own value, and which of the two wins
     * would come down to the order of an array spread.
     *
     * @var array<string, true>
     */
    private const array RESERVED = [
        'api' => true,
        'utm_source' => true,
        'utm_campaign' => true,
    ];

    /**
     * The source is the same for every link you build, so it belongs here. The
     * campaign describes one link, so this is only the default that generate()
     * overrides.
     */
    public function __construct(
        public ?string $utmSource = null,
        public ?string $utmCampaign = null,
    ) {}

    /**
     * A zoom of 0 and a query of '' have to survive, so nothing filters this
     * array. http_build_query() drops the nulls and keeps both.
     *
     * @throws InvalidOption
     */
    public function generate(Action $action, ?string $utmCampaign = null): string
    {
        $actionParameters = $action->parameters();
        $reserved = array_intersect_key($actionParameters, self::RESERVED);

        if ($reserved !== []) {
            throw InvalidOption::reservedParameters(array_keys($reserved), $action::class);
        }

        $parameters = [
            'api' => self::API_VERSION,
            ...$actionParameters,
            'utm_source' => $this->utmSource,
            'utm_campaign' => $utmCampaign ?? $this->utmCampaign,
        ];

        return self::BASE_URL.$action->endpoint().'?'.http_build_query($parameters);
    }
}
