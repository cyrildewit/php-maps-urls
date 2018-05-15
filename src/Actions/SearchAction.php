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
    protected $endpoint = 'search/';

    /**
     * @var string
     */
    protected $query;

    /**
     * @var string
     */
    protected $queryPlaceId;

    /**
     * Get the action's parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return [
            'query' => $this->query,
            'query_place_id' => $this->queryPlaceId,
        ];
    }

    /**
     * Get the search action's endpoint.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Get the search action's query.
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Get the search action's query place id.
     *
     * @return string
     */
    public function getQueryPlaceId(): string
    {
        return $this->queryPlaceId;
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
        $this->query = $lat.','.$lng;

        return $this;
    }

    /**
     * Set the search action's query place id.
     *
     * @param  string $placeId
     * @return $this
     */
    public function setQueryPlaceId(string $placeId)
    {
        $this->queryPlaceId = $placeId;

        return $this;
    }
}
