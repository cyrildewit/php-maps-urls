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

class MapsUrl
{
    /**
     * @var string
     */
    protected $baseUrl = 'https://www.google.com/maps';

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
     * Generate the url and return it.
     *
     * @return string
     */
    public function getUrl()
    {
        $parameters = $this->formatQueryString($this->collectParameters());

        return $this->baseUrl.'/'.$this->action->getEndpoint().'/?'.$parameters;
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
    protected function collectParameters()
    {
        return array_merge(['api' => $this->apiVersion], $this->action->getParameters());
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
