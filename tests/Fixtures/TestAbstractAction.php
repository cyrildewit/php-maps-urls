<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Tests\Fixtures;

use CyrildeWit\MapsUrls\Actions\AbstractAction;
use Override;

class TestAbstractAction extends AbstractAction
{
    #[Override]
    protected array $queryParametersSetters = [
        'string' => 'setStringProp',
        'int' => 'setIntProp',
        'float' => 'setFloatProp',
        'array' => 'setArrayProp',
        'pair' => 'setPairProp',
    ];

    public ?string $stringProp = null;

    public ?int $intProp = null;

    public ?float $floatProp = null;

    public ?array $arrayProp = null;

    public ?string $pairProp = null;

    public function getParameters(): array
    {
        return [];
    }

    public function getEndpoint(): string
    {
        return '';
    }

    public function getStringProp(): ?string
    {
        return $this->stringProp;
    }

    public function getIntProp(): ?int
    {
        return $this->intProp;
    }

    public function getFloatProp(): ?float
    {
        return $this->floatProp;
    }

    public function getArrayProp(): ?array
    {
        return $this->arrayProp;
    }

    public function getPairProp(): ?string
    {
        return $this->pairProp;
    }

    public function setStringProp(string $value): self
    {
        $this->stringProp = $value;

        return $this;
    }

    public function setIntProp(int $value): self
    {
        $this->intProp = $value;

        return $this;
    }

    public function setFloatProp(float $value): self
    {
        $this->floatProp = $value;

        return $this;
    }

    public function setArrayProp(array $array): self
    {
        $this->arrayProp = $array;

        return $this;
    }

    public function setPairProp(float $first, float $second): self
    {
        $this->pairProp = "{$first},{$second}";

        return $this;
    }
}
