<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Enums;

enum BaseMap: string
{
    case Roadmap = 'roadmap';
    case Satellite = 'satellite';
    case Terrain = 'terrain';
}
