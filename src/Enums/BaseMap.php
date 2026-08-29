<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Enums;

enum BaseMap: string
{
    case None = 'none';
    case Traffic = 'traffic';
    case Bicycling = 'bicycling';
}
