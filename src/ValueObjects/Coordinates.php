<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\ValueObjects;

use Stringable;

readonly class Coordinates implements Stringable
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {}

    public function __toString(): string
    {
        return $this->format($this->latitude).','.$this->format($this->longitude);
    }

    /**
     * PHP's float-to-string conversion honours the `precision` ini setting, so
     * a host running `precision=6` would silently write 47.5951518 as 47.5952,
     * a ten metre error. It also switches to exponential notation below 1e-4,
     * and Google does not read `1.0E-7` as a latitude. sprintf() avoids both.
     *
     * Seven decimals resolves to roughly a centimetre and matches the precision
     * Google's examples use. Trailing zeros are trimmed so that whole degrees
     * stay short, and a negative value that rounds away becomes plain zero.
     */
    protected function format(float $value): string
    {
        $formatted = rtrim(rtrim(sprintf('%.7F', $value), '0'), '.');

        return $formatted === '-0' ? '0' : $formatted;
    }
}
