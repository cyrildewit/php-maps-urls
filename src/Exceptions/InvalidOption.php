<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Exceptions;

use BackedEnum;
use CyrildeWit\MapsUrls\Action;
use Exception;

class InvalidOption extends Exception
{
    /**
     * @param  class-string<BackedEnum>  $enum
     */
    public static function unsupportedValue(string $queryParameter, string $enum, mixed $value): self
    {
        $expected = implode("', '", array_column($enum::cases(), 'value'));

        return new self("Invalid value provided for '{$queryParameter}'. Expected one of '{$expected}'. Received ".self::describe($value).'.');
    }

    /**
     * @param  list<string>  $supported
     */
    public static function unknownOption(string $queryParameter, array $supported): self
    {
        $expected = implode("', '", $supported);

        return new self("Unknown option '{$queryParameter}'. Expected one of '{$expected}'.");
    }

    public static function unexpectedType(string $queryParameter, string $expected, mixed $value): self
    {
        return new self("Invalid value provided for '{$queryParameter}'. Expected {$expected}. Received ".self::describe($value).'.');
    }

    public static function outOfRange(string $queryParameter, int|float $value, int|float $min, int|float $max): self
    {
        return new self("Invalid value provided for '{$queryParameter}'. Expected from {$min} to {$max}. Received '{$value}'.");
    }

    /**
     * Google matches the two lists by position, so a short list of place IDs
     * shifts every waypoint after it onto the wrong ID. The route that comes
     * back looks plausible, which is why this is worth rejecting.
     */
    public static function waypointPlaceIdCountMismatch(int $waypoints, int $placeIds): self
    {
        return new self("Invalid value provided for 'waypoint_place_ids'. Expected one place ID for each of the {$waypoints} waypoints, or none at all. Received {$placeIds}.");
    }

    /**
     * @param  list<string>  $queryParameters
     * @param  class-string<Action>  $action
     */
    public static function reservedParameters(array $queryParameters, string $action): self
    {
        $reserved = implode("', '", $queryParameters);

        return new self("Invalid parameters returned by {$action}: '{$reserved}'. The URL generator writes them, so an action cannot.");
    }

    public static function missingOption(string $queryParameter): self
    {
        return new self("Missing option '{$queryParameter}'. Google requires it.");
    }

    /**
     * @param  list<string>  $queryParameters
     */
    public static function missingOneOf(array $queryParameters): self
    {
        $expected = implode("' or '", $queryParameters);

        return new self("Missing option. Google requires '{$expected}'.");
    }

    /**
     * A place ID names a location Google has already resolved. On its own it
     * lands in the URL next to nothing, and Google reads neither.
     */
    public static function missingCompanion(string $queryParameter, string $companion): self
    {
        return new self("Missing option '{$companion}'. Google requires it alongside '{$queryParameter}'.");
    }

    protected static function describe(mixed $value): string
    {
        return is_scalar($value) ? var_export($value, return: true) : get_debug_type($value);
    }
}
