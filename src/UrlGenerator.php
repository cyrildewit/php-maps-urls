<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls;

final readonly class UrlGenerator
{
    private const string BASE_URL = 'https://www.google.com/maps/';

    private const string API_VERSION = '1';

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
     */
    public function generate(Action $action, ?string $utmCampaign = null): string
    {
        $parameters = [
            'api' => self::API_VERSION,
            ...$action->parameters(),
            'utm_source' => $this->utmSource,
            'utm_campaign' => $utmCampaign ?? $this->utmCampaign,
        ];

        return self::BASE_URL.$action->endpoint().'?'.http_build_query($parameters);
    }
}
