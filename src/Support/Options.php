<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Support;

use BackedEnum;
use CyrildeWit\MapsUrls\Coordinates;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;

/**
 * Reads one option out of an array and hands back the type the constructor
 * asks for, or throws. Every action calls these by hand from fromArray(), so
 * what each option accepts is readable in one place rather than derived from
 * the constructor signature by reflection.
 *
 * @internal
 */
final class Options
{
    /**
     * @param  array<array-key, mixed>  $options
     * @param  list<string>  $supported
     * @return array<string, mixed>
     *
     * @throws InvalidOption
     */
    public static function only(array $options, array $supported): array
    {
        $known = [];

        foreach ($options as $queryParameter => $value) {
            if (! is_string($queryParameter) || ! in_array($queryParameter, $supported, strict: true)) {
                throw InvalidOption::unknownOption((string) $queryParameter, $supported);
            }

            $known[$queryParameter] = $value;
        }

        return $known;
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @throws InvalidOption
     */
    public static function string(array $options, string $queryParameter): ?string
    {
        $value = $options[$queryParameter] ?? null;

        if ($value === null || is_string($value)) {
            return $value;
        }

        throw InvalidOption::unexpectedType($queryParameter, 'a string', $value);
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @throws InvalidOption
     */
    public static function int(array $options, string $queryParameter): ?int
    {
        $value = $options[$queryParameter] ?? null;

        if ($value === null || is_int($value)) {
            return $value;
        }

        throw InvalidOption::unexpectedType($queryParameter, 'an integer', $value);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return list<string>
     *
     * @throws InvalidOption
     */
    public static function strings(array $options, string $queryParameter): array
    {
        $expected = 'a string or a list of strings';

        return self::toList($options[$queryParameter] ?? null, $queryParameter, $expected, static fn (mixed $entry): string => is_string($entry)
                ? $entry
                : throw InvalidOption::unexpectedType($queryParameter, $expected, $entry));
    }

    /**
     * A place is a name Google can search for, or a position.
     *
     * @param  array<string, mixed>  $options
     *
     * @throws InvalidOption
     */
    public static function place(array $options, string $queryParameter): string|Coordinates|null
    {
        $value = $options[$queryParameter] ?? null;

        return $value === null ? null : self::toPlace($queryParameter, $value);
    }

    /**
     * @param  array<string, mixed>  $options
     * @return list<string|Coordinates>
     *
     * @throws InvalidOption
     */
    public static function places(array $options, string $queryParameter): array
    {
        return self::toList($options[$queryParameter] ?? null, $queryParameter, 'a place or a list of places', static fn (mixed $entry): string|Coordinates => self::toPlace($queryParameter, $entry));
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @throws InvalidOption
     */
    public static function coordinates(array $options, string $queryParameter): ?Coordinates
    {
        $value = $options[$queryParameter] ?? null;

        return match (true) {
            $value === null => null,
            $value instanceof Coordinates => $value,
            is_array($value) => self::toCoordinates($queryParameter, $value),
            default => throw InvalidOption::unexpectedType(
                $queryParameter,
                'a Coordinates instance or a [latitude, longitude] pair',
                $value,
            ),
        };
    }

    /**
     * @template T of BackedEnum
     *
     * @param  array<string, mixed>  $options
     * @param  class-string<T>  $enum
     * @return T|null
     *
     * @throws InvalidOption
     */
    public static function enum(array $options, string $queryParameter, string $enum): ?BackedEnum
    {
        $value = $options[$queryParameter] ?? null;

        return $value === null ? null : self::toEnum($queryParameter, $enum, $value);
    }

    /**
     * @template T of BackedEnum
     *
     * @param  array<string, mixed>  $options
     * @param  class-string<T>  $enum
     * @return list<T>
     *
     * @throws InvalidOption
     */
    public static function enums(array $options, string $queryParameter, string $enum): array
    {
        return self::toList($options[$queryParameter] ?? null, $queryParameter, 'a value or a list of values', static fn (mixed $entry): BackedEnum => self::toEnum($queryParameter, $enum, $entry));
    }

    /**
     * @template T
     *
     * @param  callable(mixed): T  $coerce
     * @return list<T>
     *
     * @throws InvalidOption
     */
    private static function toList(mixed $value, string $queryParameter, string $expected, callable $coerce): array
    {
        if ($value === null) {
            return [];
        }

        if (! is_array($value)) {
            return [$coerce($value)];
        }

        if (! array_is_list($value)) {
            throw InvalidOption::unexpectedType($queryParameter, $expected, $value);
        }

        return array_map($coerce, $value);
    }

    /**
     * @throws InvalidOption
     */
    private static function toPlace(string $queryParameter, mixed $value): string|Coordinates
    {
        return match (true) {
            is_string($value), $value instanceof Coordinates => $value,
            is_array($value) => self::toCoordinates($queryParameter, $value),
            default => throw InvalidOption::unexpectedType(
                $queryParameter,
                'a place name, a Coordinates instance or a [latitude, longitude] pair',
                $value,
            ),
        };
    }

    /**
     * A keyed array is rejected rather than read in insertion order, because
     * reading it in the wrong order swaps the latitude and the longitude and
     * still produces a URL that loads.
     *
     * @param  array<array-key, mixed>  $value
     *
     * @throws InvalidOption
     */
    private static function toCoordinates(string $queryParameter, array $value): Coordinates
    {
        if (! array_is_list($value) || count($value) !== 2 || ! is_numeric($value[0]) || ! is_numeric($value[1])) {
            throw InvalidOption::unexpectedType($queryParameter, 'a [latitude, longitude] pair', $value);
        }

        return new Coordinates((float) $value[0], (float) $value[1]);
    }

    /**
     * @template T of BackedEnum
     *
     * @param  class-string<T>  $enum
     * @return T
     *
     * @throws InvalidOption
     */
    private static function toEnum(string $queryParameter, string $enum, mixed $value): BackedEnum
    {
        if ($value instanceof $enum) {
            return $value;
        }

        return (is_string($value) ? $enum::tryFrom($value) : null)
            ?? throw InvalidOption::unsupportedValue($queryParameter, $enum, $value);
    }
}
