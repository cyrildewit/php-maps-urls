<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls;

use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
use CyrildeWit\MapsUrls\Support\Guard;
use Stringable;

final readonly class Coordinates implements Stringable
{
    /**
     * Seven decimals resolves to roughly a centimetre and matches the precision
     * Google's own examples use.
     */
    private const int DECIMALS = 7;

    /**
     * @throws InvalidOption
     */
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
        Guard::range('latitude', $latitude, -90, 90);
        Guard::range('longitude', $longitude, -180, 180);
    }

    /**
     * Skips the range check. A longitude past 180 wraps back around the globe
     * and Google reads it, so this is here for anyone who has one and would
     * rather not normalise it first.
     */
    public static function unchecked(float $latitude, float $longitude): self
    {
        return clone (new self(0.0, 0.0), [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    public function format(): string
    {
        return $this->formatValue($this->latitude).','.$this->formatValue($this->longitude);
    }

    public function __toString(): string
    {
        return $this->format();
    }

    /**
     * PHP's float-to-string conversion honours the `precision` ini setting, so
     * a host running `precision=6` would silently write 47.5951518 as 47.5952,
     * a ten metre error. It also switches to exponential notation below 1e-4,
     * and Google does not read `1.0E-7` as a latitude. sprintf() avoids both.
     *
     * A latitude close enough to the equator to round away keeps its sign
     * through sprintf(), which is where the -0 comes from.
     */
    private function formatValue(float $value): string
    {
        $formatted = rtrim(rtrim(sprintf('%.'.self::DECIMALS.'F', $value), '0'), '.');

        return $formatted === '-0' ? '0' : $formatted;
    }
}
