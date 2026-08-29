<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Actions;

use BackedEnum;
use CyrildeWit\MapsUrls\Enums\DirectionAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;
use Override;

class DirectionsAction extends AbstractAction
{
    const string ENDPOINT = 'dir';

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
    ];

    /** @var array<string, class-string<BackedEnum>> */
    #[Override]
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

    /** @var list<string>|null */
    protected ?array $waypoints = null;

    /** @var list<string>|null */
    protected ?array $waypointPlaceIds = null;

    /**
     * @return array<string, string|null>
     */
    public function getParameters(): array
    {
        $waypoints = $this->getWaypoints();
        $waypointPlaceIds = $this->getWaypointPlaceIds();

        return [
            'origin' => $this->getOrigin(),
            'origin_place_id' => $this->getOriginPlaceId(),
            'destination' => $this->getDestination(),
            'destination_place_id' => $this->getDestinationPlaceId(),
            'travelmode' => $this->getTravelMode()?->value,
            'dir_action' => $this->getDirectionAction()?->value,
            'waypoints' => $waypoints ? $this->formatArray($waypoints) : null,
            'waypoint_place_ids' => $waypointPlaceIds ? $this->formatArray($waypointPlaceIds) : null,
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

    /**
     * @param  list<string>  $waypoints
     */
    public function setWaypoints(array $waypoints): self
    {
        $this->waypoints = $waypoints;

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

    /**
     * @param  list<string>  $values
     */
    protected function formatArray(array $values): string
    {
        return implode('|', $values);
    }
}
