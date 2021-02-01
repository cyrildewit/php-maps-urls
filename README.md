# PHP Google Maps URLs

[![Packagist](https://img.shields.io/packagist/v/cyrildewit/php-maps-url.svg?style=flat-square)](https://packagist.org/packages/cyrildewit/php-maps-urle)
[![run-tests](https://github.com/cyrildewit/php-maps-url/workflows/run-tests/badge.svg)](https://github.com/cyrildewit/php-maps-url/actions)
[![StyleCI](https://styleci.io/repos/133079607/shield?style=flat-square)](https://styleci.io/repos/133079607)
[![Codecov branch](https://img.shields.io/codecov/c/github/cyrildewit/php-maps-url/master.svg?style=flat-square)](https://codecov.io/gh/cyrildewit/php-maps-url)
[![Total Downloads](https://img.shields.io/packagist/dt/cyrildewit/php-maps-url.svg?style=flat-square)](https://packagist.org/packages/cyrildewit/php-maps-url)
[![License](https://img.shields.io/github/license/cyrildewit/eloquentphp-maps-url.svg?style=flat-square)](https://github.com/cyrildewit/php-maps-url/blob/master/LICENSE.md)

This package allows you to build URLs for the [Google Maps URLs API](https://developers.google.com/maps/documentation/urls/guide).

Here's a quick example:

```php
use CyrildeWit\MapsUrls\UrlGenerator;
use CyrildeWit\MapsUrls\Actions\SearchAction;
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$searchAction = (new SearchAction())
    ->setQuery('The Netherlands Amsterdam');
$searchUrl = (new UrlGenerator($searchAction))->generate();

$directionsAction = (new DirectionsAction())
    ->setOrigin('The Netherlands Amsterdam')
    ->setDestination('The Netherlands Utrecht');
$directionsUrl = (new UrlGenerator($directionsAction))->generate();
```

## Overview

This package provides a convenient way to generate URLs for the Google Maps URLs API. Each action has its own abstraction that can be used to generate a URL. For more information about this API, head over to the [Google Maps URLs API documentation](https://developers.google.com/maps/documentation/urls/guide).

## Documentation

### Table of contents

1. [Getting Started](#getting-started)
    * [Requirements](#requirements)
    * [Installation](#installation)
2. [Usage](#usage)
   * [Generating a URL](#generating-a-url)
   * [Actions](#actions)
      * [Search](#search)
      * [Directions](#directions)
      * [Displaying a map](#displaying-a-map)
      * [Display a Street View panorama](#display-a-street-view-panorama)

## Getting Started

### Requirements

This package requires **PHP 7.4+**.

#### Version information

| Version | Status         | PHP Version    |
|---------|----------------|----------------|
| ^1.0    | Active support | ^7.4 and ^8.0  |
| ^0.0    | End of life    | ^7.0           |

### Installation

You can install this package via Composer using:

```sh
composer require cyrildewit/php-maps-urls
```

## Usage

### Generating a URL

The `\CyrildeWit\MapsUrls\UrlGenerator` class is responsible for generation the URLs. The constructor accepts an instance of an action class. Action classes extends `\CyrildeWit\MapsUrls\Actions\AbstractAction`.

```php
use CyrildeWit\MapsUrls\UrlGenerator;
use CyrildeWit\MapsUrls\Actions\SearchAction;

$searchAction = (new SearchAction())
    ->setQuery('Eindhoven, Nederland');
$searchUrl = (new UrlGenerator($searchAction))->generate();
```

Output `$searchUrl`: `https://www.google.com/maps/search/?api=1&query=Eindhoven,%20Nederland`

### Actions

The Google Maps URLs API allows you to generate a URL that performs a certain actions. These actions can be configured by using one of the provided action classes.

#### Search

From the official documentation: "Launch a Google Map that displays a pin for a specific place, or perform a general search and launch a map to display the results."

###### Query

To set the query of the search action, you can call the `setQuery(string $query)` method.

```php
use CyrildeWit\MapsUrls\Actions\SearchAction;

$searchAction = (new SearchAction())
    ->setQuery('Eindhoven, Nederland');
```

The query parameter may also consist of latitude/longitude coordinates. You can add them together yourself or make use of the `setCoordinates(float $latitude, float $longitude)` method.

```php
use CyrildeWit\MapsUrls\Actions\SearchAction;

$searchAction = (new SearchAction())
    ->setQueryCoordinates(47.5951518, -122.3316393);
```

###### Query Place ID

If you want to specify the optional place ID for a search action, you can add it using the `setQueryPlaceId(string $placeId)` mehod.

```php
use CyrildeWit\MapsUrls\Actions\SearchAction;

$searchAction = (new SearchAction())
    ->setQueryPlaceId('ChIJn8N5VRvZxkcRmLlkgWTSmvM');
```

###### Magic make constructor

To instantiate a search action with initial query parameters values, you can make use of the magic `SearchAction::make(array $options)` method.

```php
use CyrildeWit\MapsUrls\Actions\SearchAction;

$searchAction = SearchAction::make([
    'query' => 'Eindhoven, Nederland',
    'query_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
]);
```

#### Directions

From the official documentation: "Request directions and launch Google Maps with the results."

###### Origin

The origin can be defined using method `setOrigin(string $origin)`.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$directionsAction = (new DirectionsAction())
    ->setOrigin('Eindhoven, Nederland');
```

###### Origin Place ID

The origin place ID can be defined using method `setOriginPlaceId(string $placeId)`.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$directionsAction = (new DirectionsAction())
    ->setOrigin('Eindhoven, Nederland')
    ->setOriginPlaceId('ChIJn8N5VRvZxkcRmLlkgWTSmvM');
```

###### Destination

The destination can be defined using method `setDestination(string $destination)`.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$directionsAction = (new DirectionsAction())
    ->setDestination('Monnickendam, Nederland');
```

###### Destination Place ID

The destination place ID can be defined using method `setDestinationPlaceId(string $placeId)`.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$directionsAction = (new DirectionsAction())
    ->setDestination('Monnickendam, Nederland')
    ->setDestinationPlaceId('ChIJTZfQeLgFxkcRQhAYGf9HbrU');
```

###### Travel Mode

The travel mode can be defined using method `setTravelMode(string $travelmode)`. The valid options are:

* `driving`
* `walking`
* `bicycling`
* `transit`

These options can be referenced using the constants defined in `\CyrildeWit\MapsUrls\Enums\TravelMode`.

```php
CyrildeWit\MapsUrls\Enums\TravelMode::DRIVING;
CyrildeWit\MapsUrls\Enums\TravelMode::WALKING;
CyrildeWit\MapsUrls\Enums\TravelMode::BICYCLING;
CyrildeWit\MapsUrls\Enums\TravelMode::TRANSIT;
```

Example:

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;

$directionsAction = (new DirectionsAction())
    ->setTravelmode(TravelMode::BICYCLING);
```

The `\CyrildeWit\MapsUrls\Exceptions\InvalidTravelMode` exception will be thrown when an invalid travel mode is provided.

###### Direction Action

The direction action can be defined using method `setDirectionAction(string $directionAction)`. The only valid option is `navigate`. You can use the `NAVIGATE` constant in `DirectionAction` class for convenience.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\Enums\DirectionAction;

$directionsAction = (new DirectionsAction())
    ->setDirectionAction(DirectionAction::NAVIGATE);
```

The `\CyrildeWit\MapsUrls\Exceptions\InvalidDirectionAction` exception will be thrown when an invalid direction action is provided.

###### Waypoints

The waypoints can be defined using method `setWaypoints(array $waypoints)`.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$directionsAction = (new DirectionsAction())
    ->setWaypoints([
        'Berlin,Germany',
        'Paris,France'
    ]);
```

###### Waypoint place IDs

Waypoint place IDs can be defined using method `setWaypointPlaceIds(array $placeIds)`.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$directionsAction = (new DirectionsAction())
    ->setWaypoints([
        'Berlin,Germany',
        'Paris,France'
    ])
    ->setWaypointPlaceIds([
        'ChIJAVkDPzdOqEcRcDteW0YgIQQ',
        'ChIJD7fiBh9u5kcRYJSMaMOCCwQ'
    ]);
```

###### Magic make constructor

To instantiate a directions action with initial query parameters values, you can make use of the magic `DirectionsAction::make(array $options)` method.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;
use \CyrildeWit\MapsUrls\Enums\DirectionAction;

$directionsAction = DirectionsAction::make([
    'origin' => 'Eindhoven, Nederland',
    'origin_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
    'destination' => 'Monnickendam, Nederland',
    'destination_place_id' => 'ChIJTZfQeLgFxkcRQhAYGf9HbrU',
    'travelmode' => TravelMode::DRIVING,
    'dir_action' => DirectionAction::NAVIGATE,
    'waypoints' => [
        'Berlin,Germany',
        'Paris,France'
    ],
    'waypoint_place_ids' => [
        'ChIJAVkDPzdOqEcRcDteW0YgIQQ',
        'ChIJD7fiBh9u5kcRYJSMaMOCCwQ'
    ],
]);
```

#### Displaying a map

From the official documentation: "Launch Google Maps with no markers or directions."

###### Map action

The `map_action` query parameter is required and is therefore added by default with value `map`.

###### Center

The center of the map can be defined by setting the coordinates using method `setCenter(float $latitude, float $longitude)`.

```php
use CyrildeWit\MapsUrls\Actions\DisplayMapAction;

$displayMapAction = (new DisplayMapAction())
    ->setCenter(-33.8569, 151.2152);
```

###### Zoom

The zoom level of the map can be defined by using method `setZoom(int $zoom)`.

```php
use CyrildeWit\MapsUrls\Actions\DisplayMapAction;

$displayMapAction = (new DisplayMapAction())
    ->setZoom(10);
```

###### Base Map

The base map can be defined using method `setBaseMap(string $baseMap)`. The valid options are:

* `none`
* `traffic`
* `bicycling`

These options can be referenced using the constants defined in `\CyrildeWit\MapsUrls\Enums\TravelMode`.

```php
CyrildeWit\MapsUrls\Enums\BaseMap::NONE;
CyrildeWit\MapsUrls\Enums\BaseMap::TRAFFIC;
CyrildeWit\MapsUrls\Enums\BaseMap::BICYCLING;
```

Example:

```php
use CyrildeWit\MapsUrls\Actions\DisplayMapAction;
use CyrildeWit\MapsUrls\Enums\BaseMap;

$displayMapAction = (new DisplayMapAction())
    ->setBaseMap(BaseMap::TRAFFIC);
```

The `\CyrildeWit\MapsUrls\Exceptions\InvalidBaseMap` exception will be thrown when an invalid base map is provided.

###### Layer

The layer can be defined using method `setLayer(string $layer)`. The valid options are:

* `none`
* `transit`
* `traffic`
* `bicycling`

These options can be referenced using the constants defined in `\CyrildeWit\MapsUrls\Enums\Layer`.

```php
CyrildeWit\MapsUrls\Enums\Layer::NONE;
CyrildeWit\MapsUrls\Enums\Layer::TRANSIT;
CyrildeWit\MapsUrls\Enums\Layer::TRAFFIC;
CyrildeWit\MapsUrls\Enums\Layer::BICYCLING;
```

Example:

```php
use CyrildeWit\MapsUrls\Actions\DisplayMapAction;
use CyrildeWit\MapsUrls\Enums\Layer;

$displayMapAction = (new DisplayMapAction())
    ->setLayer(Layer::TRAFFIC);
```

The `\CyrildeWit\MapsUrls\Exceptions\InvalidLayer` exception will be thrown when an invalid layer is provided.

###### Magic make constructor

To instantiate a display street view panorama action with initial query parameters values, you can make use of the magic `DirectionsAction::make(array $options)` method.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\Enums\BaseMap;
use CyrildeWit\MapsUrls\Enums\Layer;

$displayMapAction = DirectionsAction::make([
     'center' => [-33.8569, 151.2152],
     'zoom' => 10,
     'basemap' => BaseMap::BICYCLING,
     'layer' => Layer::TRANSIT,
]);
```

#### Display a Street View panorama

From the official documentation: "Launch an interactive panorama image."

###### Map action

The `map_action` query parameter is required and is therefore added by default with value `pano`.

###### Viewpoint

The viewpoint can be defined using method `setViewpoint(float $latitude, float $longitude)`.

```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setViewpoint(48.857832, 2.295226);
```

###### Panorama ID

The panorama ID can be defined using method `setPanoramaId(string $id)`.

```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setPanoramaId('tu510ie_z4ptBZYo2BGEJg');
```

###### Heading

The heading can be defined using method `setHeading(int $degrees)`. Only values from 180 to 360 degrees are expected.


```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setHeading(120);
```

The `\CyrildeWit\MapsUrls\Exceptions\InvalidHeading` exception will be thrown when an invalid heading is provided.

###### Pitch

The pitch can be defined using method `setPitch(int $degrees)`. Only values from -90 to 80 degrees are expected.

```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setPitch(40);
```

The `\CyrildeWit\MapsUrls\Exceptions\InvalidPitch` exception will be thrown when an invalid heading is provided.

###### Fov

The pitch can be defined using method `setFov(int $degrees)`. Only values from -10 to 100 degrees are expected.

```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setFov(80);
```

The `\CyrildeWit\MapsUrls\Exceptions\InvalidFov` exception will be thrown when an invalid heading is provided.

###### Magic make constructor

To instantiate a display street view panorama action with initial query parameters values, you can make use of the magic `DirectionsAction::make(array $options)` method.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$displayStreetViewPanoramaAction = DirectionsAction::make([
    'viewpoint' => [48.857832, 2.295226],
    'pano' => 'tu510ie_z4ptBZYo2BGEJg',
    'heading' => 120,
    'pitch' => 40,
    'fov' => 80,
]);
```

## Credits

* **Cyril de Wit** - _Creator_ - [cyrildewit](https://github.com/cyrildewit)

See also the list of [contributors](https://github.com/cyrildewit/php-maps-url/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE.md) file for details.
