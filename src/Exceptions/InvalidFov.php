<?php

namespace CyrildeWit\MapsUrls\Exceptions;

use Exception;

class InvalidFov extends Exception
{
    public static function outOfRange(int $degrees): self
    {
        return new static("Invalid fov provided. Expected from -10 to 100 degrees. Received '{$degrees}`");
    }
}
