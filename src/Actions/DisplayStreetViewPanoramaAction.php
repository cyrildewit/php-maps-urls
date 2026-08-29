<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Actions;

use CyrildeWit\MapsUrls\Exceptions\InvalidOption;

class DisplayStreetViewPanoramaAction extends AbstractAction
{
    const string ENDPOINT = '@';

    const string MAP_ACTION = 'pano';

    /** @var array<string, string> */
    #[\Override]
    protected array $queryParametersSetters = [
        'viewpoint' => 'setViewpoint',
        'pano' => 'setPanoramaId',
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

    /**
     * @return array<string, string|int|null>
     */
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
        if ($this->viewpointLatitude === null || $this->viewpointLongitude === null) {
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

    /**
     * @throws InvalidOption
     */
    public function setHeading(int $degrees): self
    {
        $this->heading = $this->guardRange('heading', $degrees, -180, 360);

        return $this;
    }

    /**
     * @throws InvalidOption
     */
    public function setPitch(int $degrees): self
    {
        $this->pitch = $this->guardRange('pitch', $degrees, -90, 90);

        return $this;
    }

    /**
     * @throws InvalidOption
     */
    public function setFov(int $degrees): self
    {
        $this->fov = $this->guardRange('fov', $degrees, 10, 100);

        return $this;
    }
}
