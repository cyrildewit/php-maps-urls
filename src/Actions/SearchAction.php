<?php

namespace CyrildeWit\MapsUrls\Actions;

/*
 * This file is part of the Maps URLs package.
 *
 * (c) Cyril de Wit <github@cyrildewit.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class SearchAction implements ActionInterface
{
    /**
     * @var string
     */
    protected $endpoint = 'search';

    /**
     * @var string
     */
    protected $query;

    /**
     * @var string
     */
    protected $placeId;

    /**
     * Create a new Search Action instance.
     *
     * @param  string  $query
     * @param  string  $placeId
     */
    public function __construct(string $query = null)
    {
        $this->query = $query;
        $this->placeId = $placeId;
    }

    /**
     * Get the action's parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return [
            'query' => $this->query,
            'query_place_id' => $this->placeId,
        ];
    }

    /**
     * Get the action endpoint.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Get the action endpoint.
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Get the action endpoint.
     *
     * @return string
     */
    public function getPlaceId(): string
    {
        return $this->placeId;
    }

    /**
     * Set the search action's endpoint.
     *
     * @param  string  $endpoint
     * @return $this
     */
    public function setEndpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Set the search action's query.
     *
     * @param  string  $query
     * @return $this
     */
    public function setQuery(string $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Set the search action's query with coordinates.
     *
     * @param  float  $lat
     * @param  float  $lng
     * @return $this
     */
    public function setCoordinates(float $lat, float $lng)
    {
        $this->query = "$lat,$lng";

        return $this;
    }

    /**
     * Set the search action's place id.
     *
     * @param  string $placeId
     * @return $this
     */
    public function setPlaceId(string $placeId)
    {
        $this->placeId = $placeId;

        return $this;
    }
}
