<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls;

use CyrildeWit\MapsUrls\Actions\AbstractAction;

class UrlGenerator
{
    const string BASE_URL = 'https://www.google.com/maps/';

    const string API_VERSION = '1';

    protected ?string $utmSource = null;

    protected ?string $utmCampaign = null;

    public function __construct(protected AbstractAction $action) {}

    public function generate(): string
    {
        $parameters = $this->collectParameters();
        $queryString = $this->formatQueryString($parameters);

        return self::BASE_URL.$this->action->getEndpoint().'?'.$queryString;
    }

    public function setAction(AbstractAction $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getUtmSource(): ?string
    {
        return $this->utmSource;
    }

    public function getUtmCampaign(): ?string
    {
        return $this->utmCampaign;
    }

    public function setUtmSource(?string $source): self
    {
        $this->utmSource = $source;

        return $this;
    }

    public function setUtmCampaign(?string $campaign): self
    {
        $this->utmCampaign = $campaign;

        return $this;
    }

    /**
     * @return array<string, string|int>
     */
    protected function collectParameters(): array
    {
        $parameters = array_merge(
            $this->getDefaultParameters(),
            $this->action->getParameters(),
            $this->getTrackingParameters(),
        );

        return array_filter($parameters, static fn (string|int|null $value): bool => $value !== null);
    }

    /**
     * @return array<string, string>
     */
    protected function getDefaultParameters(): array
    {
        return [
            'api' => self::API_VERSION,
        ];
    }

    /**
     * Google asks every URL to carry the application name and the user intent
     * behind it. Both are optional, so an unset one stays out of the query
     * string rather than being guessed.
     *
     * @return array<string, string|null>
     */
    protected function getTrackingParameters(): array
    {
        return [
            'utm_source' => $this->utmSource,
            'utm_campaign' => $this->utmCampaign,
        ];
    }

    /**
     * @param  array<string, string|int>  $parameters
     */
    protected function formatQueryString(array $parameters): string
    {
        return http_build_query($parameters);
    }
}
