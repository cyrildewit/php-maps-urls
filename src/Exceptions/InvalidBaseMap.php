<?php

namespace CyrildeWit\MapsUrls\Exceptions;

use Exception;

class InvalidBaseMap extends Exception
{
    public static function unsupportedBaseMap(string $baseMap): self
    {
        return new static("Invalid travel model provided. Expected 'none', 'traffic' or 'bicycling'. Received '{$baseMap}'.");
    }
}