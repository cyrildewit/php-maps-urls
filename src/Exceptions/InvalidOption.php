<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Exceptions;

use Exception;

class InvalidOption extends Exception
{
    /**
     * @param  class-string<\BackedEnum>  $enum
     */
    public static function unsupportedValue(string $queryParameter, string $enum, string $value): self
    {
        $expected = implode("', '", array_column($enum::cases(), 'value'));

        return new self("Invalid value provided for '{$queryParameter}'. Expected one of '{$expected}'. Received '{$value}'.");
    }
}
