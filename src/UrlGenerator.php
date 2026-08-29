<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls;

use CyrildeWit\MapsUrls\Actions\AbstractAction;

class UrlGenerator
{
    const string BASE_URL = 'https://www.google.com/maps/';

    const string API_VERSION = '1';

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

    /**
     * @return array<string, string|int>
     */
    protected function collectParameters(): array
    {
        $actionParameters = $this->action->getParameters();
        $parameters = array_merge($this->getDefaultParameters(), $actionParameters);

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
     * @param  array<string, string|int>  $parameters
     */
    protected function formatQueryString(array $parameters): string
    {
        return http_build_query($parameters);
    }
}
