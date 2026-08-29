<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Actions;

use CyrildeWit\MapsUrls\Action;
use CyrildeWit\MapsUrls\Coordinates;
use CyrildeWit\MapsUrls\Enums\Avoid;
use CyrildeWit\MapsUrls\Enums\DirectionAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
use CyrildeWit\MapsUrls\Support\Options;
use Override;

/**
 * Opens a route between two places, as a preview or as turn-by-turn
 * navigation.
 */
final readonly class Directions implements Action
{
    private const string ENDPOINT = 'dir/';

    /**
     * @param  list<string|Coordinates>  $waypoints
     * @param  list<string>  $waypointPlaceIds
     * @param  list<Avoid>  $avoid
     *
     * @throws InvalidOption
     */
    public function __construct(
        public string|Coordinates|null $origin = null,
        public ?string $originPlaceId = null,
        public string|Coordinates|null $destination = null,
        public ?string $destinationPlaceId = null,
        public ?TravelMode $travelMode = null,
        public ?DirectionAction $directionAction = null,
        public array $waypoints = [],
        public array $waypointPlaceIds = [],
        public array $avoid = [],
    ) {
        if ($originPlaceId !== null && $origin === null) {
            throw InvalidOption::missingCompanion('origin_place_id', 'origin');
        }

        if ($destinationPlaceId !== null && $destination === null) {
            throw InvalidOption::missingCompanion('destination_place_id', 'destination');
        }

        if ($waypointPlaceIds !== [] && count($waypointPlaceIds) !== count($waypoints)) {
            throw InvalidOption::waypointPlaceIdCountMismatch(count($waypoints), count($waypointPlaceIds));
        }
    }

    /**
     * @param  array<array-key, mixed>  $options
     *
     * @throws InvalidOption
     */
    public static function fromArray(array $options): self
    {
        $options = Options::only($options, [
            'origin',
            'origin_place_id',
            'destination',
            'destination_place_id',
            'travelmode',
            'dir_action',
            'waypoints',
            'waypoint_place_ids',
            'avoid',
        ]);

        return new self(
            origin: Options::place($options, 'origin'),
            originPlaceId: Options::string($options, 'origin_place_id'),
            destination: Options::place($options, 'destination'),
            destinationPlaceId: Options::string($options, 'destination_place_id'),
            travelMode: Options::enum($options, 'travelmode', TravelMode::class),
            directionAction: Options::enum($options, 'dir_action', DirectionAction::class),
            waypoints: Options::places($options, 'waypoints'),
            waypointPlaceIds: Options::strings($options, 'waypoint_place_ids'),
            avoid: Options::enums($options, 'avoid', Avoid::class),
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
            'origin' => $this->origin === null ? null : (string) $this->origin,
            'origin_place_id' => $this->originPlaceId,
            'destination' => $this->destination === null ? null : (string) $this->destination,
            'destination_place_id' => $this->destinationPlaceId,
            'travelmode' => $this->travelMode?->value,
            'dir_action' => $this->directionAction?->value,
            'waypoints' => $this->waypoints === [] ? null : implode('|', $this->waypoints),
            'waypoint_place_ids' => $this->waypointPlaceIds === [] ? null : implode('|', $this->waypointPlaceIds),
            'avoid' => $this->avoid === [] ? null : implode(',', array_column($this->avoid, 'value')),
        ];
    }
}
