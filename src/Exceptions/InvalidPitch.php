<?php

namespace CyrildeWit\MapsUrls\Exceptions;

use Exception;

class InvalidPitch extends Exception
{
    public static function outOfRange(int $degrees): self
    {
        return new static("Invalid pitch provided. Expected from -90 to 80 degrees. Received '{$degrees}`");
    }
}