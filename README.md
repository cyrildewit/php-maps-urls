# PHP Google Maps URLs

[![Latest Version](https://img.shields.io/packagist/v/cyrildewit/php-maps-urls)](https://packagist.org/packages/cyrildewit/php-maps-urls)
[![Total Downloads](https://img.shields.io/packagist/dt/cyrildewit/php-maps-urls)](https://packagist.org/packages/cyrildewit/php-maps-urls)
[![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/cyrildewit/php-maps-urls/tests.yml?label=Tests)](https://github.com/cyrildewit/php-maps-urls/actions)
[![License](https://img.shields.io/packagist/l/cyrildewit/php-maps-urls)](https://packagist.org/packages/cyrildewit/php-maps-urls)
[![Coverage](https://img.shields.io/codecov/c/github/cyrildewit/php-maps-urls.svg)](https://codecov.io/gh/cyrildewit/php-maps-urls)

## Introduction

This package allows you to build URLs for the [Google Maps URLs API](https://developers.google.com/maps/documentation/urls/guide). Every action the API supports has its own class. You configure an action, hand it to the URL generator and get a string back.

The package only builds the URL string. It sends no HTTP request, and Google does not require an API key for Maps URLs. Opening the result launches the Google Maps app on Android and iOS when the app is installed, and a browser everywhere else.

### Quick example

```php
use CyrildeWit\MapsUrls\UrlGenerator;
use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;
use CyrildeWit\MapsUrls\Actions\SearchAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;

$searchAction = (new SearchAction())
    ->setQuery('Rijksmuseum');
$searchUrl = (new UrlGenerator($searchAction))->generate();
// https://www.google.com/maps/search/?api=1&query=Rijksmuseum

$directionsAction = (new DirectionsAction())
    ->setOrigin('Amsterdam')
    ->setDestination('Utrecht')
    ->setTravelMode(TravelMode::Bicycling);
$directionsUrl = (new UrlGenerator($directionsAction))->generate();
// https://www.google.com/maps/dir/?api=1&origin=Amsterdam&destination=Utrecht&travelmode=bicycling

$panoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setViewpoint(48.857832, 2.295226);
$panoramaUrl = (new UrlGenerator($panoramaAction))->generate();
// https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=48.857832%2C2.295226
```

### Key features

* A class per action: search, directions, displaying a map and Street View panoramas.
* Backed enums for every fixed-value parameter, so a typo fails at the call site instead of producing a URL that Google quietly ignores.
* A `Coordinates` value object that writes a latitude and longitude the same way on every host, whatever the `precision` ini setting is.
* Campaign tracking on the generator, shared across every action you generate with it.
* No runtime dependencies beyond PHP itself.
* Line coverage and type coverage held at 100% in CI.

<details>
<summary><strong>Table of contents</strong></summary>

1. [Introduction](#introduction)
    * [Quick example](#quick-example)
    * [Key features](#key-features)
2. [Getting Started](#getting-started)
    * [Version Compatibility](#version-compatibility)
    * [Installation](#installation)
3. [Usage](#usage)
   * [Generating a URL](#generating-a-url)
   * [Campaign tracking](#campaign-tracking)
   * [Coordinates](#coordinates)
   * [Actions](#actions)
      * [Creating an action from an array](#creating-an-action-from-an-array)
      * [Search](#search)
      * [Directions](#directions)
      * [Displaying a map](#displaying-a-map)
      * [Display a Street View panorama](#display-a-street-view-panorama)
4. [Credits](#credits)
5. [License](#license)

</details>

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

The `CyrildeWit\MapsUrls\UrlGenerator` class generates the URLs. The constructor accepts an instance of an action class. Action classes extend `CyrildeWit\MapsUrls\Actions\AbstractAction`.

```php
use CyrildeWit\MapsUrls\UrlGenerator;
use CyrildeWit\MapsUrls\Actions\SearchAction;

$searchAction = (new SearchAction())
    ->setQuery('Eindhoven, Nederland');
$searchUrl = (new UrlGenerator($searchAction))->generate();
```

Output `$searchUrl`: `https://www.google.com/maps/search/?api=1&query=Eindhoven%2C+Nederland`

The query string is built with `http_build_query()`, so a space becomes `+` and a comma becomes `%2C`. Google reads both.

One generator can build several URLs. `setAction(AbstractAction $action)` swaps the action and leaves the rest of the generator alone.

```php
use CyrildeWit\MapsUrls\UrlGenerator;
use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\Actions\SearchAction;

$generator = new UrlGenerator((new SearchAction())->setQuery('Eindhoven'));
$searchUrl = $generator->generate();

$directionsAction = (new DirectionsAction())
    ->setOrigin('Eindhoven')
    ->setDestination('Utrecht');
$directionsUrl = $generator->setAction($directionsAction)->generate();
```

Output `$directionsUrl`: `https://www.google.com/maps/dir/?api=1&origin=Eindhoven&destination=Utrecht`

Every setter on a generator or an action has a matching getter, such as `getQuery()` or `getUtmSource()`. You rarely need them when you only want a URL, but they are there for inspecting or testing an action before you generate.

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

`Coordinates` formats the pair, it does not check it. `new Coordinates(999, -500)` is accepted and lands in the URL as `999,-500`. Validate the latitude and longitude yourself if they come from user input.

### Actions

The Google Maps URLs API allows you to generate a URL that performs a certain action. These actions can be configured by using one of the provided action classes.

#### Creating an action from an array

Every action class has a static `make(array $options)` method. The keys are the query parameter names from the Google Maps URLs API, not the setter names.

```php
use CyrildeWit\MapsUrls\Actions\SearchAction;

$searchAction = SearchAction::make([
    'query' => 'Eindhoven, Nederland',
]);
```

The same rules apply to every action:

* An unknown key throws `CyrildeWit\MapsUrls\Exceptions\InvalidOption`, and the message lists the keys that action does accept.
* A parameter backed by an enum takes an enum case or the plain string behind it. `make()` resolves the string to the case backed by exactly that value, so `'driving'` works and `'Driving'` throws.
* A parameter that takes a list, such as `avoid`, also accepts a single value on its own.
* `center` and `viewpoint` take the latitude and the longitude as a two element array, or a single [`Coordinates`](#coordinates) instance.

Each action lists its own keys below.

#### Search

From the official documentation: "Launch a Google Map that displays a pin for a specific place, or perform a general search and launch a map to display the results."

##### Query

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

##### Query Place ID

If you want to specify the optional place ID for a search action, you can add it using the `setQueryPlaceId(string $placeId)` method.

```php
use CyrildeWit\MapsUrls\Actions\SearchAction;

$searchAction = (new SearchAction())
    ->setQueryPlaceId('ChIJn8N5VRvZxkcRmLlkgWTSmvM');
```

##### Creating from an array

See [creating an action from an array](#creating-an-action-from-an-array) for the shared rules.

```php
use CyrildeWit\MapsUrls\Actions\SearchAction;

$searchAction = SearchAction::make([
    'query' => 'Eindhoven, Nederland',
    'query_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
]);
```

#### Directions

From the official documentation: "Request directions and launch Google Maps with the results."

##### Origin

The origin can be defined using method `setOrigin(string|Coordinates $origin)`. It takes a place name or a [`Coordinates`](#coordinates) instance.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$directionsAction = (new DirectionsAction())
    ->setOrigin('Eindhoven, Nederland');
```

##### Origin Place ID

The origin place ID can be defined using method `setOriginPlaceId(string $placeId)`.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$directionsAction = (new DirectionsAction())
    ->setOrigin('Eindhoven, Nederland')
    ->setOriginPlaceId('ChIJn8N5VRvZxkcRmLlkgWTSmvM');
```

##### Destination

The destination can be defined using method `setDestination(string|Coordinates $destination)`. It takes a place name or a [`Coordinates`](#coordinates) instance.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$directionsAction = (new DirectionsAction())
    ->setDestination('Monnickendam, Nederland');
```

##### Destination Place ID

The destination place ID can be defined using method `setDestinationPlaceId(string $placeId)`.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;

$directionsAction = (new DirectionsAction())
    ->setDestination('Monnickendam, Nederland')
    ->setDestinationPlaceId('ChIJTZfQeLgFxkcRQhAYGf9HbrU');
```

##### Travel Mode

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
use CyrildeWit\MapsUrls\Enums\TravelMode;

$directionsAction = (new DirectionsAction())
    ->setTravelMode(TravelMode::Bicycling);
```

##### Direction Action

The direction action can be defined using method `setDirectionAction(DirectionAction $directionAction)`. The `CyrildeWit\MapsUrls\Enums\DirectionAction` enum has one case, `Navigate`.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\Enums\DirectionAction;

$directionsAction = (new DirectionsAction())
    ->setDirectionAction(DirectionAction::Navigate);
```

##### Waypoints

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

##### Waypoint place IDs

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

Google matches the two lists by position, so the first place ID belongs to the first waypoint. The package passes both lists through as you give them. It does not check that they are the same length, and a shorter list of place IDs shifts every following waypoint onto the wrong ID.

##### Avoid

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

##### Creating from an array

See [creating an action from an array](#creating-an-action-from-an-array) for the shared rules.

```php
use CyrildeWit\MapsUrls\Actions\DirectionsAction;
use CyrildeWit\MapsUrls\Enums\Avoid;
use CyrildeWit\MapsUrls\Enums\DirectionAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;

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

`travelmode`, `dir_action` and `avoid` take the plain string behind the enum case too. Because `avoid` is a list, `'avoid' => 'tolls'` and `'avoid' => ['tolls']` build the same action.

```php
$directionsAction = DirectionsAction::make([
    'travelmode' => 'driving',
    'dir_action' => 'navigate',
    'avoid' => 'tolls',
]);
```

#### Displaying a map

From the official documentation: "Launch Google Maps with no markers or directions."

##### Map action

Google requires the `map_action` parameter. This action always writes `map_action=map`, and there is no setter for it.

##### Center

The center of the map can be defined using method `setCenter(Coordinates|float $latitude, ?float $longitude = null)`. Pass a latitude and a longitude, or a single [`Coordinates`](#coordinates) instance. Passing a latitude on its own throws an `InvalidOption`.

```php
use CyrildeWit\MapsUrls\Actions\DisplayMapAction;
use CyrildeWit\MapsUrls\ValueObjects\Coordinates;

$displayMapAction = (new DisplayMapAction())
    ->setCenter(-33.8569, 151.2152);

$displayMapAction = (new DisplayMapAction())
    ->setCenter(new Coordinates(-33.8569, 151.2152));
```

`setCenterLatitude(float $latitude)` and `setCenterLongitude(float $longitude)` set one half of the pair. The `center` parameter is only written once both are set, so an action that has a latitude and no longitude generates a URL without a center rather than a half filled one.

##### Zoom

The zoom level of the map can be defined by using method `setZoom(int $zoom)`. Only whole numbers from 0 (the whole world) to 21 (individual buildings) are expected. Google notes that the upper limit varies with the map data available at the location, so a zoom of 21 is not guaranteed everywhere.

The `CyrildeWit\MapsUrls\Exceptions\InvalidOption` exception will be thrown when the zoom falls outside that range.

```php
use CyrildeWit\MapsUrls\Actions\DisplayMapAction;

$displayMapAction = (new DisplayMapAction())
    ->setZoom(10);
```

##### Base Map

The base map can be defined using method `setBaseMap(BaseMap $baseMap)`. The cases of the `CyrildeWit\MapsUrls\Enums\BaseMap` enum are:

```php
BaseMap::Roadmap;
BaseMap::Satellite;
BaseMap::Terrain;
```

Example:

```php
use CyrildeWit\MapsUrls\Actions\DisplayMapAction;
use CyrildeWit\MapsUrls\Enums\BaseMap;

$displayMapAction = (new DisplayMapAction())
    ->setBaseMap(BaseMap::Satellite);
```

##### Layer

The layer can be defined using method `setLayer(Layer $layer)`. The cases of the `CyrildeWit\MapsUrls\Enums\Layer` enum are:

```php
Layer::None;
Layer::Transit;
Layer::Traffic;
Layer::Bicycling;
```

`Layer::None` writes `layer=none`, which asks Google for a map with no layer on top. Leaving the layer unset omits the parameter instead. The two are not the same request, though they usually render the same map.

Example:

```php
use CyrildeWit\MapsUrls\Actions\DisplayMapAction;
use CyrildeWit\MapsUrls\Enums\Layer;

$displayMapAction = (new DisplayMapAction())
    ->setLayer(Layer::Traffic);
```

##### Creating from an array

See [creating an action from an array](#creating-an-action-from-an-array) for the shared rules.

```php
use CyrildeWit\MapsUrls\UrlGenerator;
use CyrildeWit\MapsUrls\Actions\DisplayMapAction;
use CyrildeWit\MapsUrls\Enums\BaseMap;
use CyrildeWit\MapsUrls\Enums\Layer;

$displayMapAction = DisplayMapAction::make([
     'center' => [-33.8569, 151.2152],
     'zoom' => 10,
     'basemap' => BaseMap::Satellite,
     'layer' => Layer::Transit,
]);
$displayMapUrl = (new UrlGenerator($displayMapAction))->generate();
```

Output `$displayMapUrl`: `https://www.google.com/maps/@?api=1&map_action=map&center=-33.8569%2C151.2152&zoom=10&basemap=satellite&layer=transit`

#### Display a Street View panorama

From the official documentation: "Launch an interactive panorama image."

##### Map action

Google requires the `map_action` parameter. This action always writes `map_action=pano`, and there is no setter for it.

##### Viewpoint

The viewpoint can be defined using method `setViewpoint(Coordinates|float $latitude, ?float $longitude = null)`. Pass a latitude and a longitude, or a single [`Coordinates`](#coordinates) instance. Passing a latitude on its own throws an `InvalidOption`.

```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;
use CyrildeWit\MapsUrls\ValueObjects\Coordinates;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setViewpoint(48.857832, 2.295226);

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setViewpoint(new Coordinates(48.857832, 2.295226));
```

`setViewpointLatitude(float $latitude)` and `setViewpointLongitude(float $longitude)` set one half of the pair. The `viewpoint` parameter is only written once both are set, so an action that has a latitude and no longitude generates a URL without a viewpoint rather than a half filled one.

##### Panorama ID

The panorama ID can be defined using method `setPanoramaId(string $id)`.

```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setPanoramaId('tu510ie_z4ptBZYo2BGEJg');
```

##### Heading

The heading can be defined using method `setHeading(int $degrees)`. Only values from -180 to 360 degrees are expected.

```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setHeading(120);
```

The `CyrildeWit\MapsUrls\Exceptions\InvalidOption` exception will be thrown when the heading falls outside that range.

##### Pitch

The pitch can be defined using method `setPitch(int $degrees)`. Only values from -90 to 90 degrees are expected.

```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setPitch(40);
```

The `CyrildeWit\MapsUrls\Exceptions\InvalidOption` exception will be thrown when the pitch falls outside that range.

##### Field of view

The field of view can be defined using method `setFov(int $degrees)`. Only values from 10 to 100 degrees are expected.

```php
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

$displayStreetViewPanoramaAction = (new DisplayStreetViewPanoramaAction())
    ->setFov(80);
```

The `CyrildeWit\MapsUrls\Exceptions\InvalidOption` exception will be thrown when the fov falls outside that range.

##### Creating from an array

See [creating an action from an array](#creating-an-action-from-an-array) for the shared rules.

```php
use CyrildeWit\MapsUrls\UrlGenerator;
use CyrildeWit\MapsUrls\Actions\DisplayStreetViewPanoramaAction;

$displayStreetViewPanoramaAction = DisplayStreetViewPanoramaAction::make([
    'viewpoint' => [48.857832, 2.295226],
    'pano' => 'tu510ie_z4ptBZYo2BGEJg',
    'heading' => 120,
    'pitch' => 40,
    'fov' => 80,
]);
$panoramaUrl = (new UrlGenerator($displayStreetViewPanoramaAction))->generate();
```

Output `$panoramaUrl`: `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=48.857832%2C2.295226&pano=tu510ie_z4ptBZYo2BGEJg&heading=120&pitch=40&fov=80`

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- **Cyril de Wit** - _Author_ - [cyrildewit](https://github.com/cyrildewit)

See also the list of [contributors](https://github.com/cyrildewit/php-maps-urls/graphs/contributors) who
participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
