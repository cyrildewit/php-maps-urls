<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Actions;

use CyrildeWit\MapsUrls\ValueObjects\Coordinates;
use Override;

class SearchAction extends AbstractAction
{
    const string ENDPOINT = 'search/';

    /** @var array<string, string> */
    #[Override]
    protected array $queryParametersSetters = [
        'query' => 'setQuery',
        'query_place_id' => 'setQueryPlaceId',
    ];

    protected ?string $query = null;

    protected ?string $queryPlaceId = null;

    /**
     * @return array<string, string|null>
     */
    public function getParameters(): array
    {
        return [
            'query' => $this->getQuery(),
            'query_place_id' => $this->getQueryPlaceId(),
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

    public function setQuery(string|Coordinates $query): self
    {
        $this->query = (string) $query;

        return $this;
    }

    public function setQueryPlaceId(string $placeId): self
    {
        $this->queryPlaceId = $placeId;

        return $this;
    }
}
