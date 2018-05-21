<?php

namespace CyrildeWit\MapsUrls;

/*
 * This file is part of the Maps URLs package.
 *
 * (c) Cyril de Wit <github@cyrildewit.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class UrlGenerator
{
    /**
     * @var string
     */
    const BASE_URL = 'https://www.google.com/maps/';

    /**
     * @var int|string
     */
    protected $apiVersion = 1;

    /**
     * @var \CyrildeWit\MapsUrls\ActionInterface|null
     */
    protected $action;

    /**
     * Create a new MapsUrl instance.
     *
     * @return void
     */
    public function __construct($action = null)
    {
        $this->action = $action;
    }

    /**
     * Generate a url from an acton instance.
     *
     * @return string
     */
    public function generate()
    {
        $parameters = $this->collectParameters();
        $queryString = $this->formatQueryString($parameters);

        return self::BASE_URL.$this->action->getEndpoint().'?'.$queryString;
    }

    /**
     * Set the URL's action.
     *
     * @param  \CyrildeWit\MapsUrls\Actions\ActionInterface  $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Set the API's version number.
     *
     * @param  string|int  $version
     * @return $this
     */
    public function setApiVersion($version)
    {
        $this->apiVersion = $version;

        return $this;
    }

    /**
     * Collect all parameters.
     *
     * @return array
     */
    protected function collectParameters(): array
    {
        $actionParameters = $this->action->getParameters();

        return array_merge($this->getDefaultParameters(), $actionParameters);
    }

    /**
     * Get default parameters.
     *
     * @return array
     */
    protected function getDefaultParameters(): array
    {
        return [
            'api' => $this->apiVersion,
        ];
    }

    /**
     * Format a parameter list to a query string.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function formatQueryString(array $parameters): string
    {
        return http_build_query($parameters);
    }
}
