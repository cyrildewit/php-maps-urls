<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Actions;

use CyrildeWit\MapsUrls\Action;
use CyrildeWit\MapsUrls\Coordinates;
use CyrildeWit\MapsUrls\Enums\BaseMap;
use CyrildeWit\MapsUrls\Enums\Layer;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
use CyrildeWit\MapsUrls\Support\Guard;
use CyrildeWit\MapsUrls\Support\Options;
use Override;

/**
 * Opens Google Maps with no markers and no directions.
 */
final readonly class DisplayMap implements Action
{
    private const string ENDPOINT = '@';

    private const string MAP_ACTION = 'map';

    /**
     * Google notes that the highest zoom it can honour varies with the map data
     * available at the location, so 21 is the highest that is ever accepted
     * rather than a level that always resolves.
     *
     * @throws InvalidOption
     */
    public function __construct(
        public ?Coordinates $center = null,
        public ?int $zoom = null,
        public ?BaseMap $baseMap = null,
        public ?Layer $layer = null,
    ) {
        Guard::range('zoom', $zoom, 0, 21);
    }

    /**
     * @param  array<array-key, mixed>  $options
     *
     * @throws InvalidOption
     */
    public static function fromArray(array $options): self
    {
        $options = Options::only($options, [
            'center',
            'zoom',
            'basemap',
            'layer',
        ]);

        return new self(
            center: Options::coordinates($options, 'center'),
            zoom: Options::int($options, 'zoom'),
            baseMap: Options::enum($options, 'basemap', BaseMap::class),
            layer: Options::enum($options, 'layer', Layer::class),
        );
    }

    #[Override]
    public function endpoint(): string
    {
        return self::ENDPOINT;
    }

    #[Override]
    public function parameters(): array
    {
        return [
            'map_action' => self::MAP_ACTION,
            'center' => $this->center?->format(),
            'zoom' => $this->zoom,
            'basemap' => $this->baseMap?->value,
            'layer' => $this->layer?->value,
        ];
    }
}
