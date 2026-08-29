<?php

namespace CyrildeWit\MapsUrls\Enums;

enum TravelMode: string
{
    case Driving = 'driving';
    case Walking = 'walking';
    case Bicycling = 'bicycling';
    case Transit = 'transit';
}
