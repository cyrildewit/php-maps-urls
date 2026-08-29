<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Actions;

use CyrildeWit\MapsUrls\Action;
use CyrildeWit\MapsUrls\Coordinates;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
use CyrildeWit\MapsUrls\Support\Options;
use Override;

/**
 * Displays a pin for a specific place, or runs a general search and shows the
 * results on a map.
 */
final readonly class Search implements Action
{
    private const string ENDPOINT = 'search/';

    /**
     * Google requires the query. A place ID narrows it, it does not replace it.
     */
    public function __construct(
        public string|Coordinates $query,
        public ?string $queryPlaceId = null,
    ) {}

    /**
     * @param  array<array-key, mixed>  $options
     *
     * @throws InvalidOption
     */
    public static function fromArray(array $options): self
    {
        $options = Options::only($options, [
            'query',
            'query_place_id',
        ]);

        return new self(
            query: Options::place($options, 'query') ?? throw InvalidOption::missingOption('query'),
            queryPlaceId: Options::string($options, 'query_place_id'),
        );
    }

    #[Override]
    public function endpoint(): string
    {
        return self::ENDPOINT;
    }

    #[Override]
    public function parameters(): array
    {
        return [
            'query' => (string) $this->query,
            'query_place_id' => $this->queryPlaceId,
        ];
    }
}
