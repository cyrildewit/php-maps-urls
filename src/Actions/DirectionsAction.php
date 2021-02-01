<?php

namespace CyrildeWit\MapsUrls\Actions;

use CyrildeWit\MapsUrls\Enums\DirectionAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;
use CyrildeWit\MapsUrls\Exceptions\InvalidDirectionAction;
use CyrildeWit\MapsUrls\Exceptions\InvalidTravelMode;

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

    protected array $travelModes = [
        TravelMode::DRIVING,
        TravelMode::WALKING,
        TravelMode::BICYCLING,
        TravelMode::TRANSIT,
    ];

    protected array $directionActions = [
        DirectionAction::NAVIGATE,
    ];

    protected ?string $origin = null;

    protected ?string $originPlaceId = null;

    protected ?string $destination = null;

    protected ?string $destinationPlaceId = null;

    protected ?string $travelMode = null;

    protected ?string $directionAction = null;

    protected ?array $waypoints = null;

    protected ?array $waypointPlaceIds = null;

    public function getParameters(): array
    {
        return [
            'origin' => $this->getOrigin(),
            'origin_place_id' => $this->getOriginPlaceId(),
            'destination' => $this->getDestination(),
            'destination_place_id' => $this->getDestinationPlaceId(),
            'travelmode' => $this->getTravelMode(),
            'dir_action' => $this->getDirectionAction(),
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

    public function getTravelMode(): ?string
    {
        return $this->travelMode;
    }

    public function getDirectionAction(): ?string
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

    public function setTravelMode(string $travelMode): self
    {
        if ($this->invalidTravelmode($travelMode)) {
            throw InvalidTravelMode::unsupportedTravelModel($travelMode);
        }

        $this->travelMode = $travelMode;

        return $this;
    }

    public function setDirectionAction(string $directionAction): self
    {
        if ($this->invalidDirectionAction($directionAction)) {
            throw InvalidDirectionAction::unsupportedDirectionAction($directionAction);
        }

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

    protected function invalidTravelMode(string $travelMode): bool
    {
        return ! in_array(strtolower($travelMode), $this->travelModes);
    }

    protected function invalidDirectionAction(string $directionAction): bool
    {
        return ! in_array(strtolower($directionAction), $this->directionActions);
    }

    protected function formatArray(array $values): string
    {
        return implode('|', $values);
    }
}
