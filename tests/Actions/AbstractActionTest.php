<?php

namespace CyrildeWit\MapsUrls\Tests\Actions;

use CyrildeWit\MapsUrls\Actions\AbstractAction;
use PHPUnit\Framework\TestCase;

class AbstractActionTest extends TestCase
{
    public function testMake()
    {
        $action = TestAbstractAction::make([
            'string' => 'foo',
            'int' => 22,
            'float' => 6.84,
            'array' => [
                'foo',
                'bar',
            ],
        ]);

        $this->assertEquals('foo', $action->getStringProp());
        $this->assertEquals(22, $action->getIntProp());
        $this->assertEquals(6.84, $action->getFloatProp());
        $this->assertEquals([
            'foo',
            'bar',
        ], $action->getArrayProp());
    }
}

class TestAbstractAction extends AbstractAction {
    protected array $queryParametersSetters = [
        'string' => 'setStringProp',
        'int' => 'setIntProp',
        'float' => 'setFloatProp',
        'array' => 'setArrayProp',
    ];

    public ?string $stringProp = null;

    public ?string $intProp = null;

    public ?float $floatProp = null;

    public ?array $arrayProp = null;

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
};
