<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Exceptions;

use Exception;

class InvalidHeading extends Exception
{
    public static function outOfRange(int $degrees): self
    {
        return new static("Invalid heading provided. Expected from -180 to 360 degrees. Received '{$degrees}'.");
    }
}
