<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls;

interface Action
{
    /**
     * The path segment that follows https://www.google.com/maps/.
     */
    public function endpoint(): string;

    /**
     * The query parameters under the names Google documents. A null value is
     * left out of the query string.
     *
     * @return array<string, string|int|null>
     */
    public function parameters(): array;
}
