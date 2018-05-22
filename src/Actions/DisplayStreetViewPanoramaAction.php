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

class DisplayStreetViewPanoramaAction extends AbstractAction
{
    /**
     * @var string
     */
    protected $endpoint = '@';

    /**
     * @var array
     */
    protected $setters = [
        'map_action' => 'setQuery',
        'viewpoint' => 'setViewpoint',
        'pano' => 'setPano',
        'heading' => 'setHeading',
        'pitch' => 'setPitch',
        'fov' => 'setFov',
    ];

    /**
     * @var string
     */
    protected $mapAction = 'pano';

    /**
     * @var string
     */
    protected $viewpoint;

    /**
     * @var string
     */
    protected $panoramaId;

    /**
     * @var int
     */
    protected $heading;

    /**
     * @var int
     */
    protected $pitch;

    /**
     * @var int
     */
    protected $fov;

    /**
     * Get the action's parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return [
            'map_action' => $this->mapAction,
            'viewpoint' => $this->viewpoint,
            'pano' => $this->panoramaId,
            'heading' => $this->heading,
            'pitch' => $this->pitch,
            'fov' => $this->fov,
        ];
    }

    /**
     * Get the street view panorama action's endpoint.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Get the street view panorama action's map action.
     *
     * @return string
     */
    public function getMapAction(): string
    {
        return $this->mapAction;
    }

    /**
     * Get the street view panorama action's viewpoint.
     *
     * @return string
     */
    public function getViewpoint()
    {
        return $this->viewpoint;
    }

    /**
     * Get the street view panorama action's panorama id.
     *
     * @return string
     */
    public function getPanoramaId()
    {
        return $this->pano;
    }

    /**
     * Get the street view panorama action's heading.
     *
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * Get the street view panorama action's pitch.
     *
     * @return string
     */
    public function getPitch()
    {
        return $this->pitch;
    }

    /**
     * Get the street view panorama action's fov.
     *
     * @return string
     */
    public function getFov()
    {
        return $this->fov;
    }

    /**
     * Set the street view panorama action's viewpoint.
     *
     * @param  float  $lat
     * @param  float  $lng
     * @return $this
     */
    public function setViewpoint(float $lat, float $lng)
    {
        $this->viewpoint = $lat.','.$lng;

        return $this;
    }

    /**
     * Set the street view panorama action's panorama id.
     *
     * @param  string  $id
     * @return $this
     */
    public function setPanoramaId(string $id)
    {
        $this->panoramaId = $id;

        return $this;
    }

    /**
     * Set the street view panorama action's heading.
     *
     * @param  int  $degrees
     * @return $this
     */
    public function setHeading(int $degrees)
    {
        if ($degrees < -180 || $degrees > 360) {
            throw new Exception('Heading is out of range: '.$degrees);
        }

        $this->heading = $degrees;

        return $this;
    }

    /**
     * Set the street view panorama action's pitch.
     *
     * @param  int  $degrees
     * @return $this
     */
    public function setPitch(int $degrees)
    {
        if ($degrees < -90 || $degrees > 80) {
            throw new Exception('Pitch is out of range: '.$degrees);
        }

        $this->pitch = $degrees;

        return $this;
    }

    /**
     * Set the street view panorama action's pitch.
     *
     * @param  int  $degrees
     * @return $this
     */
    public function setFov(int $degrees)
    {
        if ($degrees < 10 || $degrees > 100) {
            throw new Exception('Fov is out of range: '.$degrees);
        }

        $this->fov = $degrees;

        return $this;
    }
}
