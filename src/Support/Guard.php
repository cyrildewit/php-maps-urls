<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Support;

use CyrildeWit\MapsUrls\Exceptions\InvalidOption;

/**
 * @internal
 */
final class Guard
{
    /**
     * Google clamps a zoom it cannot honour and ignores a fov it cannot read,
     * so an out-of-range value produces a map rather than an error. Rejecting
     * it here is the only point at which the caller finds out.
     *
     * @throws InvalidOption
     */
    public static function range(string $queryParameter, int|float|null $value, int|float $min, int|float $max): void
    {
        if ($value !== null && ($value < $min || $value > $max)) {
            throw InvalidOption::outOfRange($queryParameter, $value, $min, $max);
        }
    }
}
