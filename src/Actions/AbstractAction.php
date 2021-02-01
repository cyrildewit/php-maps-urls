<?php

namespace CyrildeWit\MapsUrls\Actions;

abstract class AbstractAction
{
    protected array $queryParametersSetters = [];

    public static function make(array $options): self
    {
        $action = new static;
        $setters = $action->getQueryParametersSetters();

        foreach ($options as $queryParameter => $value) {
            if (isset($setters[$queryParameter])) {
                $setter = $setters[$queryParameter];

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
}
