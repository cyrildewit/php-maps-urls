<?php

namespace CyrildeWit\MapsUrls\Actions;

use CyrildeWit\MapsUrls\Exceptions\InvalidOption;

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

                call_user_func_array([$action, $setter], [$value]);
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
}
