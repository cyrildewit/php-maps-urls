# PHP Google Maps URLs

[![Packagist](https://img.shields.io/packagist/v/cyrildewit/php-maps-urls.svg?style=flat-square)](https://packagist.org/packages/cyrildewit/php-maps-urls)
[![run-tests](https://github.com/cyrildewit/php-maps-urls/workflows/run-tests/badge.svg)](https://github.com/cyrildewit/php-maps-urls/actions)
[![StyleCI](https://styleci.io/repos/133079607/shield?style=flat-square)](https://styleci.io/repos/133079607)
[![Codecov branch](https://img.shields.io/codecov/c/github/cyrildewit/php-maps-urls/master.svg?style=flat-square)](https://codecov.io/gh/cyrildewit/php-maps-urls)
[![Total Downloads](https://img.shields.io/packagist/dt/cyrildewit/php-maps-urls.svg?style=flat-square)](https://packagist.org/packages/cyrildewit/php-maps-urls)
[![License](https://img.shields.io/github/license/cyrildewit/php-maps-urls.svg?style=flat-square)](https://github.com/cyrildewit/php-maps-urls/blob/1.x/LICENSE)

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
3. [Credits](#credits)
4. [License](#license)

## Getting Started

### Version Compatibility

| Version | PHP Version |
|---------|-------------|
| ^2.0    | 8.5+        |
| ^1.0    | 7.4+        |


### Installation

You can install this package via Composer using:

```sh
composer require cyrildewit/php-maps-urls
```

## Usage

### Generating a URL

The `CyrildeWit\MapsUrls\UrlGenerator` class is responsible for generation the URLs. The constructor accepts an instance of an action class. Action classes extends `CyrildeWit\MapsUrls\Actions\AbstractAction`.

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

If you want to specify the optional place ID for a search action, you can add it using the `setQueryPlaceId(string $placeId)` method.

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

The travel mode can be defined using method `setTravelMode(TravelMode $travelMode)`. The cases of the `CyrildeWit\MapsUrls\Enums\TravelMode` enum are:

```php
CyrildeWit\MapsUrls\Enums\TravelMode::Driving;
CyrildeWit\MapsUrls\Enums\TravelMode::Walking;
CyrildeWit\MapsUrls\Enums\TravelMode::Bicycling;
CyrildeWit\MapsUrls\Enums\TravelMode::TwoWheeler;
CyrildeWit\MapsUrls\Enums\TravelMode::Transit;
```

`Bicycling` is human-powered. `TwoWheeler` covers motorised two-wheelers such as motorcycles, and Google only routes it in [countries where two-wheeler directions are supported](https://developers.google.com/maps/documentation/directions/get-directions#TwoWheeledVehicles). Elsewhere the link still opens, but the mode is ignored.

Example:

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$directionsAction = (new DirectionsAction())
    ->setTravelMode(TravelMode::Bicycling);
```

###### Direction Action

The direction action can be defined using method `setDirectionAction(DirectionAction $directionAction)`. The `CyrildeWit\MapsUrls\Enums\DirectionAction` enum has one case, `Navigate`.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$directionsAction = (new DirectionsAction())
    ->setDirectionAction(DirectionAction::Navigate);
```

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

$directionsAction = DirectionsAction::make([
    'origin' => 'Eindhoven, Nederland',
    'origin_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
    'destination' => 'Monnickendam, Nederland',
    'destination_place_id' => 'ChIJTZfQeLgFxkcRQhAYGf9HbrU',
    'travelmode' => TravelMode::Driving,
    'dir_action' => DirectionAction::Navigate,
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

`travelmode` and `dir_action` also accept a plain string, which `make()` resolves to an enum case without regard to casing. A string that matches no case throws `CyrildeWit\MapsUrls\Exceptions\InvalidOption`.

```php
$directionsAction = DirectionsAction::make([
    'travelmode' => 'driving',
    'dir_action' => 'navigate',
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

The zoom level of the map can be defined by using method `setZoom(int $zoom)`. Only whole numbers from 0 (the whole world) to 21 (individual buildings) are expected. Google notes that the upper limit varies with the map data available at the location, so a zoom of 21 is not guaranteed everywhere.

The `CyrildeWit\MapsUrls\Exceptions\InvalidOption` exception will be thrown when the zoom falls outside that range.

```php
use CyrildeWit\MapsUrls\Actions\DisplayMapAction;

$displayMapAction = (new DisplayMapAction())
    ->setZoom(10);
```

###### Base Map

The base map can be defined using method `setBaseMap(BaseMap $baseMap)`. The cases of the `CyrildeWit\MapsUrls\Enums\BaseMap` enum are:

```php
CyrildeWit\MapsUrls\Enums\BaseMap::Roadmap;
CyrildeWit\MapsUrls\Enums\BaseMap::Satellite;
CyrildeWit\MapsUrls\Enums\BaseMap::Terrain;
```

Example:

```php
use CyrildeWit\MapsUrls\Actions\DisplayMapAction;

$displayMapAction = (new DisplayMapAction())
    ->setBaseMap(BaseMap::Satellite);
```

###### Layer

The layer can be defined using method `setLayer(Layer $layer)`. The cases of the `CyrildeWit\MapsUrls\Enums\Layer` enum are:

```php
CyrildeWit\MapsUrls\Enums\Layer::None;
CyrildeWit\MapsUrls\Enums\Layer::Transit;
CyrildeWit\MapsUrls\Enums\Layer::Traffic;
CyrildeWit\MapsUrls\Enums\Layer::Bicycling;
```

Example:

```php
use CyrildeWit\MapsUrls\Actions\DisplayMapAction;

$displayMapAction = (new DisplayMapAction())
    ->setLayer(Layer::Traffic);
```

###### Magic make constructor

To instantiate a display street view panorama action with initial query parameters values, you can make use of the magic `DirectionsAction::make(array $options)` method.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$displayMapAction = DirectionsAction::make([
     'center' => [-33.8569, 151.2152],
     'zoom' => 10,
     'basemap' => BaseMap::Satellite,
     'layer' => Layer::Transit,
]);
```

`basemap` and `layer` also accept a plain string, which `make()` resolves to an enum case without regard to casing. A string that matches no case throws `CyrildeWit\MapsUrls\Exceptions\InvalidOption`.

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

The heading can be defined using method `setHeading(int $degrees)`. Only values from -180 to 360 degrees are expected.

```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setHeading(120);
```

The `CyrildeWit\MapsUrls\Exceptions\InvalidOption` exception will be thrown when the heading falls outside that range.

###### Pitch

The pitch can be defined using method `setPitch(int $degrees)`. Only values from -90 to 90 degrees are expected.

```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setPitch(40);
```

The `CyrildeWit\MapsUrls\Exceptions\InvalidOption` exception will be thrown when the pitch falls outside that range.

###### Fov

The fov can be defined using method `setFov(int $degrees)`. Only values from 10 to 100 degrees are expected.

```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setFov(80);
```

The `CyrildeWit\MapsUrls\Exceptions\InvalidOption` exception will be thrown when the fov falls outside that range.

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

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
