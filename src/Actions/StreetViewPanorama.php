<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Actions;

use CyrildeWit\MapsUrls\Action;
use CyrildeWit\MapsUrls\Coordinates;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
use CyrildeWit\MapsUrls\Support\Guard;
use CyrildeWit\MapsUrls\Support\Options;
use Override;

/**
 * Opens the ground-level imagery at a location rather than the map above it.
 */
final readonly class StreetViewPanorama implements Action
{
    private const string ENDPOINT = '@';

    private const string MAP_ACTION = 'pano';

    /**
     * Google needs somewhere to point the camera, so one of the viewpoint and
     * the panorama ID has to be present. Giving both is fine: the panorama ID
     * wins, and the viewpoint is used only when Google cannot find it.
     *
     * @throws InvalidOption
     */
    public function __construct(
        public ?Coordinates $viewpoint = null,
        public ?string $panoramaId = null,
        public ?int $heading = null,
        public ?int $pitch = null,
        public ?int $fov = null,
    ) {
        if (! $viewpoint instanceof Coordinates && $panoramaId === null) {
            throw InvalidOption::missingOneOf(['viewpoint', 'pano']);
        }

        Guard::range('heading', $heading, -180, 360);
        Guard::range('pitch', $pitch, -90, 90);
        Guard::range('fov', $fov, 10, 100);
    }

    /**
     * @param  array<array-key, mixed>  $options
     *
     * @throws InvalidOption
     */
    public static function fromArray(array $options): self
    {
        $options = Options::only($options, [
            'viewpoint',
            'pano',
            'heading',
            'pitch',
            'fov',
        ]);

        return new self(
            viewpoint: Options::coordinates($options, 'viewpoint'),
            panoramaId: Options::string($options, 'pano'),
            heading: Options::int($options, 'heading'),
            pitch: Options::int($options, 'pitch'),
            fov: Options::int($options, 'fov'),
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
            'viewpoint' => $this->viewpoint?->format(),
            'pano' => $this->panoramaId,
            'heading' => $this->heading,
            'pitch' => $this->pitch,
            'fov' => $this->fov,
        ];
    }
}
