<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Actions;

use CyrildeWit\MapsUrls\Exceptions\InvalidOption;
use ReflectionMethod;

abstract class AbstractAction
{
    protected array $queryParametersSetters = [];

    protected array $queryParametersEnums = [];

    /**
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

    abstract public function getParameters(): array;

    abstract public function getEndpoint(): string;

    public function getQueryParametersSetters(): array
    {
        return $this->queryParametersSetters;
    }

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
