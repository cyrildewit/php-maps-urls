<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Exceptions;

use Exception;

class InvalidOption extends Exception
{
    /**
     * @param  class-string<\BackedEnum>  $enum
     */
    public static function unsupportedValue(string $queryParameter, string $enum, mixed $value): self
    {
        $expected = implode("', '", array_column($enum::cases(), 'value'));
        $received = is_scalar($value) ? var_export($value, true) : get_debug_type($value);

        return new self("Invalid value provided for '{$queryParameter}'. Expected one of '{$expected}'. Received {$received}.");
    }

    /**
     * @param  list<string>  $supported
     */
    public static function unknownOption(string $queryParameter, array $supported): self
    {
        $expected = implode("', '", $supported);

        return new self("Unknown option '{$queryParameter}'. Expected one of '{$expected}'.");
    }
}
