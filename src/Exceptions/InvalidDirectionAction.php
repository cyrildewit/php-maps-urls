<?php

namespace CyrildeWit\MapsUrls\Exceptions;

use Exception;

class InvalidDirectionAction extends Exception
{
    public static function unsupportedDirectionAction(string $directionAction): self
    {
        return new static("Invalid direction action provided. Expected 'navigate', received '{$directionAction}'.");
    }
}
