<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Actions;

use CyrildeWit\MapsUrls\Enums\DirectionAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;

class DirectionsAction extends AbstractAction
{
    const ENDPOINT = 'dir';

    protected array $queryParametersSetters = [
        'origin' => 'setOrigin',
        'origin_place_id' => 'setOriginPlaceId',
        'destination' => 'setDestination',
        'destination_place_id' => 'setDestinationPlaceId',
        'travelmode' => 'setTravelMode',
        'dir_action' => 'setDirectionAction',
        'waypoints' => 'setWaypoints',
        'waypoint_place_ids' => 'setWaypointPlaceIds',
    ];

    protected array $queryParametersEnums = [
        'travelmode' => TravelMode::class,
        'dir_action' => DirectionAction::class,
    ];

    protected ?string $origin = null;

    protected ?string $originPlaceId = null;

    protected ?string $destination = null;

    protected ?string $destinationPlaceId = null;

    protected ?TravelMode $travelMode = null;

    protected ?DirectionAction $directionAction = null;

    protected ?array $waypoints = null;

    protected ?array $waypointPlaceIds = null;

    public function getParameters(): array
    {
        return [
            'origin' => $this->getOrigin(),
            'origin_place_id' => $this->getOriginPlaceId(),
            'destination' => $this->getDestination(),
            'destination_place_id' => $this->getDestinationPlaceId(),
            'travelmode' => $this->getTravelMode()?->value,
            'dir_action' => $this->getDirectionAction()?->value,
            'waypoints' => $this->hasWaypoints() ? $this->formatArray($this->getWaypoints()) : null,
            'waypoint_place_ids' => $this->hasWaypointPlaceIds() ? $this->formatArray($this->getWaypointPlaceIds()) : null,
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

    public function getWaypoints(): ?array
    {
        return $this->waypoints;
    }

    public function getWaypointPlaceIds(): ?array
    {
        return $this->waypointPlaceIds;
    }

    public function hasWaypoints(): bool
    {
        return ! empty($this->waypoints);
    }

    public function hasWaypointPlaceIds(): bool
    {
        return ! empty($this->waypointPlaceIds);
    }

    public function setOrigin(string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function setOriginPlaceId(string $placeId): self
    {
        $this->originPlaceId = $placeId;

        return $this;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;

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

    public function setWaypoints(array $waypoints): self
    {
        $this->waypoints = $waypoints;

        return $this;
    }

    public function setWaypointPlaceIds(array $placeIds): self
    {
        $this->waypointPlaceIds = $placeIds;

        return $this;
    }

    protected function formatArray(array $values): string
    {
        return implode('|', $values);
    }
}
