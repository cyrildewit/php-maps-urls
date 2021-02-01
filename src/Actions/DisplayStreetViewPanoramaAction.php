<?php

namespace CyrildeWit\MapsUrls\Actions;

use CyrildeWit\MapsUrls\Exceptions\InvalidFov;
use CyrildeWit\MapsUrls\Exceptions\InvalidHeading;
use CyrildeWit\MapsUrls\Exceptions\InvalidPitch;

class DisplayStreetViewPanoramaAction extends AbstractAction
{
    const ENDPOINT = '@';
    const MAP_ACTION = 'pano';

    protected array $queryParametersSetters = [
        'viewpoint' => 'setViewpoint',
        'pano' => 'setPano',
        'heading' => 'setHeading',
        'pitch' => 'setPitch',
        'fov' => 'setFov',
    ];

    protected ?float $viewpointLatitude = null;
    protected ?float $viewpointLongitude = null;

    protected ?string $panoramaId = null;

    protected ?int $heading = null;

    protected ?int $pitch = null;

    protected ?int $fov = null;

    public function getParameters(): array
    {
        return [
            'map_action' => $this->getMapAction(),
            'viewpoint' => $this->getViewpoint(),
            'pano' => $this->getPanoramaId(),
            'heading' => $this->getHeading(),
            'pitch' => $this->getPitch(),
            'fov' => $this->getFov(),
        ];
    }

    public function getEndpoint(): string
    {
        return self::ENDPOINT;
    }

    public function getMapAction(): string
    {
        return self::MAP_ACTION;
    }

    public function getViewpoint(): ?string
    {
        if (empty($this->viewpointLatitude) || empty($this->viewpointLongitude)) {
            return null;
        }

        return "{$this->viewpointLatitude},{$this->viewpointLongitude}";
    }

    public function getPanoramaId(): ?string
    {
        return $this->panoramaId;
    }

    public function getHeading(): ?int
    {
        return $this->heading;
    }

    public function getPitch(): ?int
    {
        return $this->pitch;
    }

    public function getFov(): ?int
    {
        return $this->fov;
    }

    public function setViewpoint(float $latitude, float $longitude): self
    {
        $this->setViewpointLatitude($latitude);
        $this->setViewpointLongitude($longitude);

        return $this;
    }

    public function setViewpointLatitude(float $latitude): self
    {
        $this->viewpointLatitude = $latitude;

        return $this;
    }

    public function setViewpointLongitude(float $longitude): self
    {
        $this->viewpointLongitude = $longitude;

        return $this;
    }

    public function setPanoramaId(string $id): self
    {
        $this->panoramaId = $id;

        return $this;
    }

    public function setHeading(int $degrees): self
    {
        if ($degrees < -180 || $degrees > 360) {
            throw InvalidHeading::outOfRange($degrees);
        }

        $this->heading = $degrees;

        return $this;
    }

    public function setPitch(int $degrees): self
    {
        if ($degrees < -90 || $degrees > 80) {
            throw InvalidPitch::outOfRange($degrees);
        }

        $this->pitch = $degrees;

        return $this;
    }

    public function setFov(int $degrees): self
    {
        if ($degrees < 10 || $degrees > 100) {
            throw InvalidFov::outOfRange($degrees);
        }

        $this->fov = $degrees;

        return $this;
    }
}
