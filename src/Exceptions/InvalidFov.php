<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Exceptions;

use Exception;

class InvalidFov extends Exception
{
    public static function outOfRange(int $degrees): self
    {
        return new self("Invalid fov provided. Expected from 10 to 100 degrees. Received '{$degrees}'.");
    }
}
