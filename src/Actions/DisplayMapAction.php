<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Actions;

use CyrildeWit\MapsUrls\Enums\BaseMap;
use CyrildeWit\MapsUrls\Enums\Layer;

class DisplayMapAction extends AbstractAction
{
    const string ENDPOINT = '@';

    const string MAP_ACTION = 'map';

    /** @var array<string, string> */
    #[\Override]
    protected array $queryParametersSetters = [
        'center' => 'setCenter',
        'zoom' => 'setZoom',
        'basemap' => 'setBaseMap',
        'layer' => 'setLayer',
    ];

    /** @var array<string, class-string<\BackedEnum>> */
    #[\Override]
    protected array $queryParametersEnums = [
        'basemap' => BaseMap::class,
        'layer' => Layer::class,
    ];

    protected ?float $centerLatitude = null;

    protected ?float $centerLongitude = null;

    protected ?int $zoom = null;

    protected ?BaseMap $baseMap = null;

    protected ?Layer $layer = null;

    /**
     * @return array<string, string|int|null>
     */
    public function getParameters(): array
    {
        return [
            'map_action' => $this->getMapAction(),
            'center' => $this->getCenter(),
            'zoom' => $this->getZoom(),
            'basemap' => $this->getBaseMap()?->value,
            'layer' => $this->getLayer()?->value,
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
        if ($this->centerLatitude === null || $this->centerLongitude === null) {
            return null;
        }

        return "{$this->centerLatitude},{$this->centerLongitude}";
    }

    public function getZoom(): ?int
    {
        return $this->zoom;
    }

    public function getBaseMap(): ?BaseMap
    {
        return $this->baseMap;
    }

    public function getLayer(): ?Layer
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
        $this->zoom = $zoom;

        return $this;
    }

    public function setBaseMap(BaseMap $baseMap): self
    {
        $this->baseMap = $baseMap;

        return $this;
    }

    public function setLayer(Layer $layer): self
    {
        $this->layer = $layer;

        return $this;
    }
}
