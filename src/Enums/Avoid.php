<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Enums;

enum Avoid: string
{
    case Ferries = 'ferries';
    case Highways = 'highways';
    case Tolls = 'tolls';
}
