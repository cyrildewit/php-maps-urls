<?php

namespace CyrildeWit\MapsUrls\Actions;

use CyrildeWit\MapsUrls\Enums\BaseMap;
use CyrildeWit\MapsUrls\Enums\Layer;
use CyrildeWit\MapsUrls\Exceptions\InvalidBaseMap;
use CyrildeWit\MapsUrls\Exceptions\InvalidLayer;

class DisplayMapAction extends AbstractAction
{
    const ENDPOINT = '@';
    const MAP_ACTION = 'map';

    protected array $queryParametersSetters = [
        'center' => 'setCenter',
        'zoom' => 'setZoom',
        'basemap' => 'setBaseMap',
        'layer' => 'setLayer',
    ];

    protected array $baseMaps = [
        BaseMap::NONE,
        BaseMap::TRAFFIC,
        BaseMap::BICYCLING,
    ];

    protected array $layers = [
        Layer::NONE,
        Layer::TRANSIT,
        Layer::TRAFFIC,
        Layer::BICYCLING,
    ];

    protected ?float $centerLatitude = null;
    protected ?float $centerLongitude = null;

    protected ?int $zoom = null;

    protected ?string $baseMap = null;

    protected ?string $layer = null;

    public function getParameters(): array
    {
        return [
            'map_action' => $this->getMapAction(),
            'center' => $this->getCenter(),
            'zoom' => $this->getZoom(),
            'basemap' => $this->getBaseMap(),
            'layer' => $this->getLayer(),
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

    public function getCenter(): ?string
    {
        if (empty($this->centerLatitude) || empty($this->centerLongitude)) {
            return null;
        }

        return "{$this->centerLatitude},{$this->centerLongitude}";
    }

    public function getZoom(): ?string
    {
        return $this->zoom;
    }

    public function getBaseMap(): ?string
    {
        return $this->baseMap;
    }

    public function getLayer(): ?string
    {
        return $this->layer;
    }

    public function setCenter(float $latitude, float $longitude): self
    {
        $this->setCenterLatitude($latitude);
        $this->setCenterLongitude($longitude);

        return $this;
    }

    public function setCenterLatitude(float $latitude): self
    {
        $this->centerLatitude = $latitude;

        return $this;
    }

    public function setCenterLongitude(float $longitude): self
    {
        $this->centerLongitude = $longitude;

        return $this;
    }

    public function setZoom(int $zoom): self
    {
        $this->zoom = "{$zoom}";

        return $this;
    }

    public function setBaseMap(string $baseMap): self
    {
        if ($this->invalidBaseMap($baseMap)) {
            throw InvalidBaseMap::unsupportedBaseMap($baseMap);
        }

        $this->baseMap = $baseMap;

        return $this;
    }

    public function setLayer(string $layer): self
    {
        if ($this->invalidLayer($layer)) {
            throw InvalidLayer::unsupportedLayer($layer);
        }

        $this->layer = $layer;

        return $this;
    }

    protected function invalidBaseMap(string $baseMap): bool
    {
        return ! in_array(strtolower($baseMap), $this->baseMaps);
    }

    protected function invalidLayer(string $layer): bool
    {
        return ! in_array(strtolower($layer), $this->layers);
    }
}
