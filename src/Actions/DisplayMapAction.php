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

use Exception;

class DisplayMapAction implements ActionInterface
{
    /**
     * @var string
     */
    protected $endpoint = 'maps/@';

    /**
     * Collection of supported basemaps.
     *
     * @var array
     */
    protected $basemaps = [
        'none', 'traffic', 'bicycling',
    ];

    /**
     * Collection of supported layers.
     *
     * @var array
     */
    protected $layers = [
        'none', 'transit', 'traffic', 'bicycling',
    ];

    /**
     * @var string
     */
    protected $mapAction = 'map';

    /**
     * @var string
     */
    protected $center;

    /**
     * @var int
     */
    protected $zoom;

    /**
     * @var string
     */
    protected $basemap;

    /**
     * @var string
     */
    protected $layer;

    /**
     * Get the display map action's parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return [
            'map_action' => $this->mapAction,
            'center' => $this->center,
            'zoom' => $this->zoom,
            'basemap' => $this->basemap,
            'layer' => $this->layer,
        ];
    }

    /**
     * Get the display map action's endpoint.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Get the display map action's map action.
     *
     * @return string
     */
    public function getMapAction()
    {
        return $this->mapAction;
    }

    /**
     * Get the display map action's center.
     *
     * @return string
     */
    public function getCenter()
    {
        return $this->center;
    }

    /**
     * Get the display map action's basemap .
     *
     * @return string
     */
    public function getBasemap()
    {
        return $this->basemap;
    }

    /**
     * Get the display map action's layer .
     *
     * @return string
     */
    public function getLayer()
    {
        return $this->layer;
    }

    /**
     * Set the display map action's center.
     *
     * @param  float  $lat
     * @param  float  $lng
     * @return $this
     */
    public function setCenter(float $lat, float $lng)
    {
        $this->center = $lat.','.$lng;

        return $this;
    }

    /**
     * Set the display map action's zoom.
     *
     * @param  int  $zoom
     * @return $this
     */
    public function setZoom(int $zoom)
    {
        $this->zoom = $zoom;

        return $this;
    }

    /**
     * Set the display map action's basemap.
     *
     * @param  string  $basemap
     * @return $this
     */
    public function setBasemap(string $basemap)
    {
        if ($this->invalidBasemap($basemap)) {
            throw new Exception('Invalid basemap: '.$basemap);
        }

        // Since this parameter is optional and optional, we don't have to add
        // it to the URL
        if ($basemap === 'none') {
            return $this;
        }

        $this->basemap = $basemap;

        return $this;
    }

    /**
     * Set the display map action's layer.
     *
     * @param  string  $layer
     * @return $this
     */
    public function setLayer(string $layer)
    {
        if ($this->invalidLayer($layer)) {
            throw new Exception('Invalid layer: '.$layer);
        }

        // Since this parameter is optional and optional, we don't have to add
        // it to the URL
        if ($layer === 'none') {
            return $this;
        }

        $this->layer = $layer;

        return $this;
    }

    /**
     * Determine if the given basemap is supported.
     *
     * @param  string  $basemap
     * @return bool
     */
    protected function invalidBasemap(string $basemap)
    {
        return ! in_array(strtolower($basemap), $this->basemaps);
    }

    /**
     * Determine if the given layer is supported.
     *
     * @param  string  $layer
     * @return bool
     */
    protected function invalidLayer(string $layer)
    {
        return ! in_array(strtolower($layer), $this->layers);
    }
}
