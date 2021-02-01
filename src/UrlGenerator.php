<?php

namespace CyrildeWit\MapsUrls;

use CyrildeWit\MapsUrls\Actions\AbstractAction;

class UrlGenerator
{
    const BASE_URL = 'https://www.google.com/maps/';
    const API_VERSION = '1';

    protected AbstractAction $action;

    public function __construct(AbstractAction $action)
    {
        $this->action = $action;
    }

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

    protected function collectParameters(): array
    {
        $actionParameters = $this->action->getParameters();

        return array_merge($this->getDefaultParameters(), $actionParameters);
    }

    protected function getDefaultParameters(): array
    {
        return [
            'api' => self::API_VERSION,
        ];
    }

    protected function formatQueryString(array $parameters): string
    {
        return http_build_query($parameters);
    }
}
