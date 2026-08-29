<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Actions;

use BackedEnum;
use CyrildeWit\MapsUrls\Enums\Avoid;
use CyrildeWit\MapsUrls\Enums\DirectionAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;
use CyrildeWit\MapsUrls\ValueObjects\Coordinates;
use Override;

class DirectionsAction extends AbstractAction
{
    const string ENDPOINT = 'dir/';

    /** @var array<string, string> */
    #[Override]
    protected array $queryParametersSetters = [
        'origin' => 'setOrigin',
        'origin_place_id' => 'setOriginPlaceId',
        'destination' => 'setDestination',
        'destination_place_id' => 'setDestinationPlaceId',
        'travelmode' => 'setTravelMode',
        'dir_action' => 'setDirectionAction',
        'waypoints' => 'setWaypoints',
        'waypoint_place_ids' => 'setWaypointPlaceIds',
        'avoid' => 'setAvoid',
    ];

    /** @var array<string, class-string<BackedEnum>> */
    #[Override]
    protected array $queryParametersEnums = [
        'travelmode' => TravelMode::class,
        'dir_action' => DirectionAction::class,
        'avoid' => Avoid::class,
    ];

    protected ?string $origin = null;

    protected ?string $originPlaceId = null;

    protected ?string $destination = null;

    protected ?string $destinationPlaceId = null;

    protected ?TravelMode $travelMode = null;

    protected ?DirectionAction $directionAction = null;

    /** @var list<string>|null */
    protected ?array $waypoints = null;

    /** @var list<string>|null */
    protected ?array $waypointPlaceIds = null;

    /** @var list<Avoid>|null */
    protected ?array $avoid = null;

    /**
     * @return array<string, string|null>
     */
    public function getParameters(): array
    {
        $waypoints = $this->getWaypoints();
        $waypointPlaceIds = $this->getWaypointPlaceIds();
        $avoid = $this->getAvoid();

        return [
            'origin' => $this->getOrigin(),
            'origin_place_id' => $this->getOriginPlaceId(),
            'destination' => $this->getDestination(),
            'destination_place_id' => $this->getDestinationPlaceId(),
            'travelmode' => $this->getTravelMode()?->value,
            'dir_action' => $this->getDirectionAction()?->value,
            'waypoints' => $waypoints ? $this->formatArray($waypoints) : null,
            'waypoint_place_ids' => $waypointPlaceIds ? $this->formatArray($waypointPlaceIds) : null,
            'avoid' => $avoid ? $this->formatAvoid($avoid) : null,
        ];
    }

    public function getEndpoint(): string
    {
        return self::ENDPOINT;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function getOriginPlaceId(): ?string
    {
        return $this->originPlaceId;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getDestinationPlaceId(): ?string
    {
        return $this->destinationPlaceId;
    }

    public function getTravelMode(): ?TravelMode
    {
        return $this->travelMode;
    }

    public function getDirectionAction(): ?DirectionAction
    {
        return $this->directionAction;
    }

    /**
     * @return list<string>|null
     */
    public function getWaypoints(): ?array
    {
        return $this->waypoints;
    }

    /**
     * @return list<string>|null
     */
    public function getWaypointPlaceIds(): ?array
    {
        return $this->waypointPlaceIds;
    }

    /**
     * @return list<Avoid>|null
     */
    public function getAvoid(): ?array
    {
        return $this->avoid;
    }

    public function hasWaypoints(): bool
    {
        return ! empty($this->waypoints);
    }

    public function hasWaypointPlaceIds(): bool
    {
        return ! empty($this->waypointPlaceIds);
    }

    public function hasAvoid(): bool
    {
        return ! empty($this->avoid);
    }

    public function setOrigin(string|Coordinates $origin): self
    {
        $this->origin = (string) $origin;

        return $this;
    }

    public function setOriginPlaceId(string $placeId): self
    {
        $this->originPlaceId = $placeId;

        return $this;
    }

    public function setDestination(string|Coordinates $destination): self
    {
        $this->destination = (string) $destination;

        return $this;
    }

    public function setDestinationPlaceId(string $placeId): self
    {
        $this->destinationPlaceId = $placeId;

        return $this;
    }

    public function setTravelMode(TravelMode $travelMode): self
    {
        $this->travelMode = $travelMode;

        return $this;
    }

    public function setDirectionAction(DirectionAction $directionAction): self
    {
        $this->directionAction = $directionAction;

        return $this;
    }

    /**
     * @param  list<string|Coordinates>  $waypoints
     */
    public function setWaypoints(array $waypoints): self
    {
        $this->waypoints = array_map(
            static fn (string|Coordinates $waypoint): string => (string) $waypoint,
            $waypoints,
        );

        return $this;
    }

    /**
     * @param  list<string>  $placeIds
     */
    public function setWaypointPlaceIds(array $placeIds): self
    {
        $this->waypointPlaceIds = $placeIds;

        return $this;
    }

    public function setAvoid(Avoid ...$avoid): self
    {
        $this->avoid = array_values($avoid);

        return $this;
    }

    /**
     * @param  list<string>  $values
     */
    protected function formatArray(array $values): string
    {
        return implode('|', $values);
    }

    /**
     * @param  list<Avoid>  $avoid
     */
    protected function formatAvoid(array $avoid): string
    {
        return implode(',', array_column($avoid, 'value'));
    }
}
