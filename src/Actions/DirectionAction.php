<?php

namespace CyrildeWit\MapsUrls\Actions;

/*
 * This file is part of the Maps URLs package.
 *
 * (c) Cyril de Wit <github@cyrildewit.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Exception;

class DirectionAction extends AbstractAction
{
    /**
     * @var string
     */
    protected $endpoint = 'dir';

    /**
     * @var array
     */
    protected $setters = [
        'origin' => 'setOrigin',
        'origin_place_id' => 'setOriginPlaceId',
        'destination' => 'setDestination',
        'destination_place_id' => 'setDestinationPlaceId',
        'travelmode' => 'setTravelmode',
        'dir_action' => 'setDirectionAction',
        'waypoints' => 'setWaypoints',
        'waypoint_place_ids' => 'setWaypointPlaceIds',
    ];

    /**
     * @var array
     */
    protected $travelmodes = [
        'driving', 'walking', 'bicycling', 'transit',
    ];

    /**
     * @var string
     */
    protected $origin;

    /**
     * @var string
     */
    protected $originPlaceId;

    /**
     * @var string
     */
    protected $destination;

    /**
     * @var string
     */
    protected $destinationPlaceId;

    /**
     * @var string
     */
    protected $travelmode;

    /**
     * @var string
     */
    protected $directionAction;

    /**
     * @var string
     */
    protected $waypoints;

    /**
     * @var array
     */
    protected $waypointPlaceIds;

    /**
     * Get the direction action's parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return [
            'origin' => $this->origin,
            'origin_place_id' => $this->originPlaceId,
            'destination' => $this->destination,
            'destination_place_id' => $this->destinationPlaceId,
            'travelmode' => $this->travelmode,
            'dir_action' => $this->directionAction,
            'waypoints' => $this->waypoints ? $this->formatArray($this->waypoints) : null,
            'waypoint_place_ids' => $this->waypointPlaceIds ? $this->formatArray($this->waypointPlaceIds) : null,
        ];
    }

    /**
     * Get the direction action's endpoint.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Get the direction action's origin.
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Get the direction action's origin place id.
     *
     * @return string
     */
    public function getOriginPlaceId()
    {
        return $this->originPlaceId;
    }

    /**
     * Get the direction action's destination.
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Get the direction action's destination place id.
     *
     * @return string
     */
    public function getDestinationPlaceId()
    {
        return $this->destinationPlaceId;
    }

    /**
     * Get the direction action's travelmode.
     *
     * @return string
     */
    public function getTravelmode()
    {
        return $this->travelmode;
    }

    /**
     * Get the direction action's direction action.
     *
     * @return string
     */
    public function getDirectionAction()
    {
        return $this->directionAction;
    }

    /**
     * Get the direction action's waypoints.
     *
     * @return array
     */
    public function getWaypoints()
    {
        return $this->waypoints;
    }

    /**
     * Get the direction action's waypoint place ids.
     *
     * @return array
     */
    public function getWaypointPlaceIds()
    {
        return $this->waypointPlaceIds;
    }

    /**
     * Set the direction action's origin.
     *
     * @param  string  $origin
     * @return $this
     */
    public function setOrigin(string $origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Set the direction action's origin place id.
     *
     * @param  string  $placeId
     * @return $this
     */
    public function setOriginPlaceId(string $placeId)
    {
        $this->originPlaceId = $placeId;

        return $this;
    }

    /**
     * Set the direction action's destination.
     *
     * @param  string  $destination
     * @return $this
     */
    public function setDestination(string $destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * Set the direction action's destination place id.
     *
     * @param  string  $placeId
     * @return $this
     */
    public function setDestinationPlaceId(string $placeId)
    {
        $this->destinationPlaceId = $placeId;

        return $this;
    }

    /**
     * Set the direction action's destination place id.
     *
     * @param  string  $travelmode
     * @return $this
     */
    public function setTravelmode(string $travelmode)
    {
        if ($this->invalidTravelmode($travelmode)) {
            throw new Exception('Invalid travelmode: '.$travelmode);
        }

        $this->travelmode = $travelmode;

        return $this;
    }

    /**
     * Set the direction action's destination action.
     *
     * @param  string  $directionAction
     * @return $this
     */
    public function setDirectionAction(string $directionAction)
    {
        if ($directionAction !== 'navigate') {
            throw new Exception('Invalid direction action: '.$directionAction);
        }

        $this->directionAction = $directionAction;

        return $this;
    }

    /**
     * Set the direction action's waypoints.
     *
     * @param  string  $waypoints
     * @return $this
     */
    public function setWaypoints(array $waypoints)
    {
        $this->waypoints = $waypoints;

        return $this;
    }

    /**
     * Set the direction action's waypoint place ids.
     *
     * @param  string  $placeIds
     * @return $this
     */
    public function setWaypointPlaceIds(array $placeIds)
    {
        $this->waypointPlaceIds =  $placeIds;

        return $this;
    }

    /**
     * Determine if the given travelmode is supported.
     *
     * @param  string  $travelmode
     * @return bool
     */
    protected function invalidTravelmode(string $travelmode)
    {
        return ! in_array(strtolower($travelmode), $this->travelmodes);
    }

    /**
     * Format the given array.
     *
     * @param  array  $values
     * @return string
     */
    protected function formatArray(array $values): string
    {
        return implode('|', $values);
    }
}
