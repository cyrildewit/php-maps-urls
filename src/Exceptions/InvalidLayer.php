<?php

namespace CyrildeWit\MapsUrls\Exceptions;

use Exception;

class InvalidLayer extends Exception
{
    public static function unsupportedLayer(string $layer): self
    {
        return new static("Invalid layer provided. Expected 'none', 'transit', 'traffic' or 'bicycling'. Received '{$layer}'.");
    }
}