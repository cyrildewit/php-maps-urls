<?php

namespace CyrildeWit\MapsUrls\Exceptions;

use Exception;

class InvalidTravelMode extends Exception
{
    public static function unsupportedTravelModel(string $travelMode): self
    {
        return new static("Invalid travel model provided. Expected 'driving', 'walking', 'bicycling' or 'transit'. Received '{$travelMode}`");
    }
}
