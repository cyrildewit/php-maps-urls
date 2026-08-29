<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Actions;

use BackedEnum;
use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
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
            if (isset($setters[$queryParameter])) {
                $setter = $setters[$queryParameter];

                if (is_string($value) && isset($enums[$queryParameter])) {
                    $enum = $enums[$queryParameter];

                    $value = $enum::tryFrom(strtolower($value))
                        ?? throw InvalidOption::unsupportedValue($queryParameter, $enum, $value);
                }

                $arguments = is_array($value) && $action->setterTakesMultipleArguments($setter)
                    ? $value
                    : [$value];

                $action->{$setter}(...$arguments);
            }
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
     * Setters like setCenter() take a latitude and a longitude rather than one
     * value, so make() has to spread the option over both parameters.
     */
    protected function setterTakesMultipleArguments(string $setter): bool
    {
        return (new ReflectionMethod($this, $setter))->getNumberOfParameters() > 1;
    }
}
