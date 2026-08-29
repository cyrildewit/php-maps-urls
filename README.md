# PHP Google Maps URLs

[![Latest Version](https://img.shields.io/packagist/v/cyrildewit/php-maps-urls)](https://packagist.org/packages/cyrildewit/php-maps-urls)
[![Total Downloads](https://img.shields.io/packagist/dt/cyrildewit/php-maps-urls)](https://packagist.org/packages/cyrildewit/php-maps-urls)
[![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/cyrildewit/php-maps-urls/tests.yml?label=Tests)](https://github.com/cyrildewit/php-maps-urls/actions)
[![License](https://img.shields.io/packagist/l/cyrildewit/php-maps-urls)](https://packagist.org/packages/cyrildewit/php-maps-urls)
[![Coverage](https://img.shields.io/codecov/c/github/cyrildewit/php-maps-urls.svg)](https://codecov.io/gh/cyrildewit/php-maps-urls)

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
   * [Campaign tracking](#campaign-tracking)
   * [Coordinates](#coordinates)
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

### Campaign tracking

Google asks every URL to carry two tracking parameters. `utm_source` is the name of your application, and `utm_campaign` is the intent behind the link, such as `directions_request`. Set them with `setUtmSource(?string $source)` and `setUtmCampaign(?string $campaign)`.

```php
use CyrildeWit\MapsUrls\UrlGenerator;
use CyrildeWit\MapsUrls\Actions\SearchAction;

$searchAction = (new SearchAction())
    ->setQuery('Eindhoven');

$searchUrl = (new UrlGenerator($searchAction))
    ->setUtmSource('my_app')
    ->setUtmCampaign('search_request')
    ->generate();
```

Output `$searchUrl`: `https://www.google.com/maps/search/?api=1&query=Eindhoven&utm_source=my_app&utm_campaign=search_request`

Both are optional and independent. A parameter you never set stays out of the query string, and passing `null` removes one you set earlier without touching the other. The package will not invent a source name on your behalf.

The tracking parameters survive a call to `setAction()`, so one generator can serve several actions under the same campaign.

### Coordinates

Six parameters take a latitude/longitude pair. Four of them accept a place name instead: `query`, `origin`, `destination` and `waypoints`. The other two, `center` and `viewpoint`, are always a pair. Every one of them accepts a `CyrildeWit\MapsUrls\ValueObjects\Coordinates` instance.

```php
use CyrildeWit\MapsUrls\Actions\SearchAction;
use CyrildeWit\MapsUrls\ValueObjects\Coordinates;

$searchAction = (new SearchAction())
    ->setQuery(new Coordinates(47.5951518, -122.3316393));
```

`Coordinates` writes the pair with at most seven decimals, the precision Google uses in its own examples, which resolves to about a centimetre. It drops trailing zeros, so `new Coordinates(41, 2)` becomes `41,2`, and it rounds away anything below the seventh decimal.

Format the pair through this class rather than interpolating the floats yourself. Casting a float to a string honours the `precision` ini setting, so a host running `precision=6` writes `47.5951518` as `47.5952`, a ten metre error in a URL that still looks right. The cast also switches to exponential notation below `1e-4`, and Google does not read `1.0E-7` as a latitude.

### Actions

The Google Maps URLs API allows you to generate a URL that performs a certain actions. These actions can be configured by using one of the provided action classes.

#### Search

From the official documentation: "Launch a Google Map that displays a pin for a specific place, or perform a general search and launch a map to display the results."

###### Query

To set the query of the search action, you can call the `setQuery(string|Coordinates $query)` method.

```php
use CyrildeWit\MapsUrls\Actions\SearchAction;

$searchAction = (new SearchAction())
    ->setQuery('Eindhoven, Nederland');
```

The query parameter may also consist of latitude/longitude coordinates. Pass a [`Coordinates`](#coordinates) instance for those.

```php
use CyrildeWit\MapsUrls\Actions\SearchAction;
use CyrildeWit\MapsUrls\ValueObjects\Coordinates;

$searchAction = (new SearchAction())
    ->setQuery(new Coordinates(47.5951518, -122.3316393));
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

The origin can be defined using method `setOrigin(string|Coordinates $origin)`. It takes a place name or a [`Coordinates`](#coordinates) instance.

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

The destination can be defined using method `setDestination(string|Coordinates $destination)`. It takes a place name or a [`Coordinates`](#coordinates) instance.

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
TravelMode::Driving;
TravelMode::Walking;
TravelMode::Bicycling;
TravelMode::TwoWheeler;
TravelMode::Transit;
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

The waypoints can be defined using method `setWaypoints(array $waypoints)`. Each entry is a place name or a [`Coordinates`](#coordinates) instance, and the two may be mixed in one list.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\ValueObjects\Coordinates;

$directionsAction = (new DirectionsAction())
    ->setWaypoints([
        'Berlin,Germany',
        new Coordinates(48.8566, 2.3522)
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

###### Avoid

The route features to avoid can be defined using method `setAvoid(Avoid ...$avoid)`. The cases of the `CyrildeWit\MapsUrls\Enums\Avoid` enum are:

```php
Avoid::Ferries;
Avoid::Highways;
Avoid::Tolls;
```

Google treats these as a preference rather than a rule. A route that cannot avoid the feature is still returned.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\Enums\Avoid;

$directionsAction = (new DirectionsAction())
    ->setAvoid(Avoid::Tolls, Avoid::Ferries);
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
    'avoid' => [
        Avoid::Tolls,
        Avoid::Ferries
    ],
]);
```

`travelmode`, `dir_action` and `avoid` also accept a plain string, which `make()` resolves to the enum case backed by exactly that value. Anything else, casing included, throws `CyrildeWit\MapsUrls\Exceptions\InvalidOption`.

```php
$directionsAction = DirectionsAction::make([
    'travelmode' => 'driving',
    'dir_action' => 'navigate',
    'avoid' => ['tolls', 'ferries'],
]);
```

Because `avoid` takes a list, `make()` accepts one value on its own as well. `'avoid' => 'tolls'` and `'avoid' => ['tolls']` build the same action.

#### Displaying a map

From the official documentation: "Launch Google Maps with no markers or directions."

###### Map action

The `map_action` query parameter is required and is therefore added by default with value `map`.

###### Center

The center of the map can be defined using method `setCenter(Coordinates|float $latitude, ?float $longitude = null)`. Pass a latitude and a longitude, or a single [`Coordinates`](#coordinates) instance. Passing a latitude on its own throws an `InvalidOption`.

```php
use CyrildeWit\MapsUrls\Actions\DisplayMapAction;
use CyrildeWit\MapsUrls\ValueObjects\Coordinates;

$displayMapAction = (new DisplayMapAction())
    ->setCenter(-33.8569, 151.2152);

$displayMapAction = (new DisplayMapAction())
    ->setCenter(new Coordinates(-33.8569, 151.2152));
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
BaseMap::Roadmap;
BaseMap::Satellite;
BaseMap::Terrain;
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
Layer::None;
Layer::Transit;
Layer::Traffic;
Layer::Bicycling;
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

`basemap` and `layer` also accept a plain string, which `make()` resolves to the enum case backed by exactly that value. Anything else, casing included, throws `CyrildeWit\MapsUrls\Exceptions\InvalidOption`.

#### Display a Street View panorama

From the official documentation: "Launch an interactive panorama image."

###### Map action

The `map_action` query parameter is required and is therefore added by default with value `pano`.

###### Viewpoint

The viewpoint can be defined using method `setViewpoint(Coordinates|float $latitude, ?float $longitude = null)`. Pass a latitude and a longitude, or a single [`Coordinates`](#coordinates) instance. Passing a latitude on its own throws an `InvalidOption`.

```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;
use CyrildeWit\MapsUrls\ValueObjects\Coordinates;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setViewpoint(48.857832, 2.295226);

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setViewpoint(new Coordinates(48.857832, 2.295226));
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

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- **Cyril de Wit** - _Author_ - [cyrildewit](https://github.com/cyrildewit)

See also the list of [contributors](https://github.com/cyrildewit/eloquent-viewable/graphs/contributors) who
participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
