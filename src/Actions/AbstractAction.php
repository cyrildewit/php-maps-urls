<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Actions;

use BackedEnum;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
use CyrildeWit\MapsUrls\ValueObjects\Coordinates;
use ReflectionMethod;

/**
 * @phpstan-consistent-constructor
 */
abstract class AbstractAction
{
    /** @var array<string, string> */
    protected array $queryParametersSetters = [];

    /** @var array<string, class-string<BackedEnum>> */
    protected array $queryParametersEnums = [];

    /**
     * @param  array<string, mixed>  $options
     *
     * @throws InvalidOption
     */
    public static function make(array $options): self
    {
        $action = new static;
        $setters = $action->getQueryParametersSetters();
        $enums = $action->getQueryParametersEnums();

        foreach ($options as $queryParameter => $value) {
            $setter = $setters[$queryParameter]
                ?? throw InvalidOption::unknownOption($queryParameter, array_keys($setters));

            $enum = $enums[$queryParameter] ?? null;

            if ($enum !== null) {
                $value = $action->setterTakesList($setter)
                    ? array_map(
                        static fn (mixed $case): BackedEnum => static::resolveEnum($queryParameter, $enum, $case),
                        is_array($value) ? $value : [$value],
                    )
                    : static::resolveEnum($queryParameter, $enum, $value);
            }

            $arguments = is_array($value) && $action->setterTakesMultipleArguments($setter)
                ? $value
                : [$value];

            $action->{$setter}(...$arguments);
        }

        return $action;
    }

    /**
     * @return array<string, string|int|null>
     */
    abstract public function getParameters(): array;

    abstract public function getEndpoint(): string;

    /**
     * @return array<string, string>
     */
    public function getQueryParametersSetters(): array
    {
        return $this->queryParametersSetters;
    }

    /**
     * @return array<string, class-string<BackedEnum>>
     */
    public function getQueryParametersEnums(): array
    {
        return $this->queryParametersEnums;
    }

    /**
     * @param  class-string<BackedEnum>  $enum
     *
     * @throws InvalidOption
     */
    protected static function resolveEnum(string $queryParameter, string $enum, mixed $value): BackedEnum
    {
        if ($value instanceof BackedEnum && $value::class === $enum) {
            return $value;
        }

        return (is_string($value) ? $enum::tryFrom($value) : null)
            ?? throw InvalidOption::unsupportedValue($queryParameter, $enum, $value);
    }

    /**
     * Setters like setCenter() take a latitude and a longitude rather than one
     * value, so make() has to spread the option over both parameters.
     */
    protected function setterTakesMultipleArguments(string $setter): bool
    {
        $method = new ReflectionMethod($this, $setter);

        return $method->getNumberOfParameters() > 1 || $method->isVariadic();
    }

    protected function setterTakesList(string $setter): bool
    {
        return new ReflectionMethod($this, $setter)->isVariadic();
    }

    /**
     * Setters like setCenter() accept either a Coordinates instance or a
     * separate latitude and longitude, so both call styles have to end up as
     * one pair.
     *
     * @throws InvalidOption
     */
    protected function toCoordinates(string $queryParameter, Coordinates|float $latitude, ?float $longitude): Coordinates
    {
        if ($latitude instanceof Coordinates) {
            return $latitude;
        }

        return new Coordinates(
            $latitude,
            $longitude ?? throw InvalidOption::missingLongitude($queryParameter),
        );
    }

    /**
     * @throws InvalidOption
     */
    protected function guardRange(string $queryParameter, int $value, int $min, int $max): int
    {
        if ($value < $min || $value > $max) {
            throw InvalidOption::outOfRange($queryParameter, $value, $min, $max);
        }

        return $value;
    }
}
