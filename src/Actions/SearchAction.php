<?php

namespace CyrildeWit\MapsUrls\Actions;

class SearchAction extends AbstractAction
{
    const ENDPOINT = 'search/';

    protected array $queryParametersSetters = [
        'query' => 'setQuery',
        'query_coordinates' => 'setQueryCoordinates',
        'query_place_id' => 'setQueryPlaceId',
    ];

    protected ?string $query = null;

    protected ?string $queryPlaceId = null;

    public function getParameters(): array
    {
        return [
            'query' => $this->query,
            'query_place_id' => $this->queryPlaceId,
        ];
    }

    public function getEndpoint(): string
    {
        return self::ENDPOINT;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function getQueryPlaceId(): ?string
    {
        return $this->queryPlaceId;
    }

    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function setQueryCoordinates(float $latitude, float $longitude): self
    {
        $this->query = "{$latitude},{$longitude}";

        return $this;
    }

    public function setQueryPlaceId(string $placeId): self
    {
        $this->queryPlaceId = $placeId;

        return $this;
    }
}
