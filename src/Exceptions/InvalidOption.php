<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Exceptions;

use BackedEnum;
use Exception;

class InvalidOption extends Exception
{
    /**
     * @param  class-string<BackedEnum>  $enum
     */
    public static function unsupportedValue(string $queryParameter, string $enum, mixed $value): self
    {
        $expected = implode("', '", array_column($enum::cases(), 'value'));
        $received = is_scalar($value) ? var_export($value, return: true) : get_debug_type($value);

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

    public static function outOfRange(string $queryParameter, int $value, int $min, int $max): self
    {
        return new self("Invalid value provided for '{$queryParameter}'. Expected from {$min} to {$max}. Received '{$value}'.");
    }
}
