<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls;

/**
 * The short way to build one URL without campaign tracking. Reach for
 * UrlGenerator when you want utm_source on every link you build.
 */
final class MapsUrl
{
    public static function for(Action $action): string
    {
        return new UrlGenerator()->generate($action);
    }
}
