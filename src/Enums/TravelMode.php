<?php

declare(strict_types=1);

namespace CyrildeWit\MapsUrls\Enums;

enum TravelMode: string
{
    case Driving = 'driving';
    case Walking = 'walking';
    case Bicycling = 'bicycling';
    case TwoWheeler = 'two-wheeler';
    case Transit = 'transit';
}
