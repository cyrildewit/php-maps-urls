<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Enums;

enum Layer: string
{
    case None = 'none';
    case Transit = 'transit';
    case Traffic = 'traffic';
    case Bicycling = 'bicycling';
}
