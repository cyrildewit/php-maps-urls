<div align="center">
  <h3 align="center">PHP Google Maps URLs</h3>
  <p align="center">
    Generate URLs for the Google Maps URLs API
  </p>
  <br/>
  <p align="center">
    <a href="https://packagist.org/packages/cyrildewit/php-maps-urls"><img alt="Latest Version" src="https://img.shields.io/packagist/v/cyrildewit/php-maps-urls"/></a>
    <a href="https://packagist.org/packages/cyrildewit/php-maps-urls"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/cyrildewit/php-maps-urls"/></a>
    <a href="https://github.com/cyrildewit/php-maps-urls/actions"><img alt="GitHub Actions Workflow Status" src="https://img.shields.io/github/actions/workflow/status/cyrildewit/php-maps-urls/tests.yml?label=Tests"/></a>
    <a href="https://packagist.org/packages/cyrildewit/php-maps-urls"><img alt="License" src="https://img.shields.io/packagist/l/cyrildewit/php-maps-urls"/></a>
    <a href="https://codecov.io/gh/cyrildewit/php-maps-urls"><img alt="Coverage" src="https://img.shields.io/codecov/c/github/cyrildewit/php-maps-urls.svg"/></a>
  </p>
</div>
<hr/>

<details>
<summary>Table of Contents</summary>

1. [Introduction](#introduction)
2. [Getting Started](#getting-started)
    - [Version Compatibility](#version-compatibility)
    - [Installation](#installation)
3. [Usage](#usage)
    - [Generating a URL](#generating-a-url)
    - [Campaign tracking](#campaign-tracking)
    - [Coordinates](#coordinates)
    - [Actions](#actions)
        - [Creating an action from an array](#creating-an-action-from-an-array)
        - [Search](#search)
        - [Directions](#directions)
        - [DisplayMap](#displaymap)
        - [StreetViewPanorama](#streetviewpanorama)
4. [Changelog](#changelog)
5. [Contributing](#contributing)
6. [Credits](#credits)
7. [License](#license)

</details>

## Introduction

**PHP Google Maps URLs** builds URLs for the [Google Maps URLs API](https://developers.google.com/maps/documentation/urls/guide). Every action the API supports has its own class. You construct one and hand it to `MapsUrl::for()`, which gives you a string back.

The package only builds the URL string. It sends no HTTP request, and Google does not require an API key for Maps URLs. Opening the result launches the Google Maps app on Android and iOS when the app is installed, and a browser everywhere else.

### Quick Example

Once installed, generating a URL looks like this:

```php
use CyrildeWit\MapsUrls\MapsUrl;
use CyrildeWit\MapsUrls\Actions\Directions;
use CyrildeWit\MapsUrls\Actions\Search;
use CyrildeWit\MapsUrls\Actions\StreetViewPanorama;
use CyrildeWit\MapsUrls\Enums\TravelMode;
use CyrildeWit\MapsUrls\Coordinates;

$searchUrl = MapsUrl::for(new Search(query: 'Rijksmuseum'));
// https://www.google.com/maps/search/?api=1&query=Rijksmuseum

$directionsUrl = MapsUrl::for(new Directions(
    origin: 'Amsterdam',
    destination: 'Utrecht',
    travelMode: TravelMode::Bicycling,
));
// https://www.google.com/maps/dir/?api=1&origin=Amsterdam&destination=Utrecht&travelmode=bicycling

$panoramaUrl = MapsUrl::for(new StreetViewPanorama(
    viewpoint: new Coordinates(48.857832, 2.295226),
));
// https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=48.857832%2C2.295226
```

### Key Features

- A class per action: search, directions, displaying a map and Street View panoramas.
- Actions are immutable and built with named arguments, so a misspelled parameter fails at the call site rather than producing a URL Google quietly ignores.
- Backed enums for every fixed-value parameter.
- A `Coordinates` value object that writes a latitude and longitude the same way on every host, whatever the `precision` ini setting is, and rejects a position that is not on Earth.
- Campaign tracking through `UrlGenerator`, shared across every link you build with it.
- No runtime dependencies beyond PHP itself.
- Line coverage and type coverage held at 100% in CI.

## Getting Started

### Version Compatibility

| Package Version                                                        | PHP  |
|------------------------------------------------------------------------|------|
| [2.x](https://packagist.org/packages/cyrildewit/php-maps-urls#2.x-dev) | 8.5+ |
| [1.x](https://packagist.org/packages/cyrildewit/php-maps-urls#1.x-dev) | 7.4+ |

### Installation

First, you need to install the package via Composer:

```sh
composer require cyrildewit/php-maps-urls:^2
```

## Usage

### Generating a URL

`CyrildeWit\MapsUrls\MapsUrl::for()` takes an action and returns the URL.

```php
use CyrildeWit\MapsUrls\MapsUrl;
use CyrildeWit\MapsUrls\Actions\Search;

$searchUrl = MapsUrl::for(new Search(query: 'Eindhoven, Nederland'));
// https://www.google.com/maps/search/?api=1&query=Eindhoven%2C+Nederland
```

Pass the parameters as named arguments and leave out the ones you do not need.

### Campaign tracking

Google asks every URL to carry two tracking parameters. `utm_source` is the name of your application, and `utm_campaign` is the intent behind the link, such as `directions_request`.

Build a `CyrildeWit\MapsUrls\UrlGenerator` with the source and generate through it. The source is the same for every link you build, so it belongs on the generator.

```php
use CyrildeWit\MapsUrls\UrlGenerator;
use CyrildeWit\MapsUrls\Actions\Directions;
use CyrildeWit\MapsUrls\Actions\Search;

$maps = new UrlGenerator(utmSource: 'my_app');

$searchUrl = $maps->generate(
    new Search(query: 'Eindhoven'),
    utmCampaign: 'search_request',
);
// https://www.google.com/maps/search/?api=1&query=Eindhoven&utm_source=my_app&utm_campaign=search_request

$directionsUrl = $maps->generate(
    new Directions(origin: 'Eindhoven', destination: 'Utrecht'),
    utmCampaign: 'directions_request',
);
// https://www.google.com/maps/dir/?api=1&origin=Eindhoven&destination=Utrecht&utm_source=my_app&utm_campaign=directions_request
```

The campaign describes one link, so `generate()` takes it per call. Pass `utmCampaign` to the constructor as well if most of your links share one, and the argument to `generate()` overrides it.

Both parameters are optional and independent. One you never set stays out of the query string. The package will not invent a source name on your behalf.

A generator holds nothing but these two strings and never changes, so one instance can serve your whole application.

### Coordinates

Six parameters take a latitude/longitude pair. Four of them accept a place name instead: `query`, `origin`, `destination` and `waypoints`. The other two, `center` and `viewpoint`, are always a pair. Every one of them accepts a `CyrildeWit\MapsUrls\Coordinates` instance.

```php
use CyrildeWit\MapsUrls\Actions\Search;
use CyrildeWit\MapsUrls\Coordinates;

$action = new Search(query: new Coordinates(47.5951518, -122.3316393));
```

`Coordinates` writes the pair with at most seven decimals, the precision Google uses in its own examples, which resolves to about a centimetre. It drops trailing zeros, so `new Coordinates(41, 2)` becomes `41,2`, and it rounds away anything below the seventh decimal.

> [!WARNING]
> Format the pair through this class rather than interpolating the floats yourself. Casting a float to a string
> honours the `precision` ini setting, so a host running `precision=6` writes `47.5951518` as `47.5952`, a ten metre
> error in a URL that still looks right. The cast also switches to exponential notation below `1e-4`, and Google does
> not read `1.0E-7` as a latitude.

A latitude outside -90 to 90 or a longitude outside -180 to 180 throws `CyrildeWit\MapsUrls\Exceptions\InvalidOption`. A longitude past the antimeridian wraps back around the globe and Google reads it, so `Coordinates::unchecked()` skips the check for anyone who has one and would rather not normalise it first.

```php
Coordinates::unchecked(48.857832, 542.295226);
```

### Actions

The Google Maps URLs API allows you to generate a URL that performs a certain action. Each action has its own class.

#### Creating an action from an array

Every action class has a static `fromArray(array $options)` method for options that arrive at runtime, from configuration or from JSON. The keys are the query parameter names from the Google Maps URLs API, not the constructor argument names. When you know the options while writing the code, use named arguments instead and let the compiler check them.

```php
use CyrildeWit\MapsUrls\Actions\Search;

$action = Search::fromArray([
    'query' => 'Eindhoven, Nederland',
]);
```

The same rules apply to every action:

- An unknown key throws `CyrildeWit\MapsUrls\Exceptions\InvalidOption`, and the message lists the keys that action does accept.
- A value of the wrong type throws `InvalidOption` rather than a `TypeError`.
- A parameter backed by an enum takes an enum case or the plain string behind it. The string has to match the case exactly, so `'driving'` works and `'Driving'` throws.
- A parameter that takes a list, such as `avoid` or `waypoints`, also accepts a single value on its own.
- Anywhere coordinates are accepted, you can pass a [`Coordinates`](#coordinates) instance or a two element `[latitude, longitude]` array.

> [!NOTE]
> A keyed coordinates array is rejected, because reading it in the wrong order swaps the latitude and the longitude and
> still produces a URL that loads.

Each action lists its own keys below.

#### Search

> Launch a Google Map that displays a pin for a specific place, or perform a general search and launch a map to
> display the results.
>
> [Google Maps URLs documentation](https://developers.google.com/maps/documentation/urls/get-started#search-action)

`CyrildeWit\MapsUrls\Actions\Search` takes:

| Argument       | Option           | Type                      |
|----------------|------------------|---------------------------|
| `query`        | `query`          | `string` or `Coordinates` |
| `queryPlaceId` | `query_place_id` | `string` or `null`        |

Google requires the query, so it has no default. A place ID narrows a query rather than replacing one, and `fromArray()` without a `query` key throws `InvalidOption`.

```php
use CyrildeWit\MapsUrls\Actions\Search;
use CyrildeWit\MapsUrls\Coordinates;

$action = new Search(
    query: 'Eindhoven, Nederland',
    queryPlaceId: 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
);

$action = new Search(query: new Coordinates(47.5951518, -122.3316393));
```

##### Creating from an array

See [creating an action from an array](#creating-an-action-from-an-array) for the shared rules.

```php
use CyrildeWit\MapsUrls\MapsUrl;
use CyrildeWit\MapsUrls\Actions\Search;

$searchUrl = MapsUrl::for(Search::fromArray([
    'query' => 'Eindhoven, Nederland',
    'query_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
]));
// https://www.google.com/maps/search/?api=1&query=Eindhoven%2C+Nederland&query_place_id=ChIJn8N5VRvZxkcRmLlkgWTSmvM
```

#### Directions

> Request directions and launch Google Maps with the results.
>
> [Google Maps URLs documentation](https://developers.google.com/maps/documentation/urls/get-started#directions-action)

`CyrildeWit\MapsUrls\Actions\Directions` takes:

| Argument               | Option                  | Type                              |
|------------------------|-------------------------|-----------------------------------|
| `origin`               | `origin`                | `string`, `Coordinates` or `null` |
| `originPlaceId`        | `origin_place_id`       | `string` or `null`                |
| `destination`          | `destination`           | `string`, `Coordinates` or `null` |
| `destinationPlaceId`   | `destination_place_id`  | `string` or `null`                |
| `travelMode`           | `travelmode`            | `TravelMode` or `null`            |
| `directionAction`      | `dir_action`            | `DirectionAction` or `null`       |
| `waypoints`            | `waypoints`             | list of `string` or `Coordinates` |
| `waypointPlaceIds`     | `waypoint_place_ids`    | list of `string`                  |
| `avoid`                | `avoid`                 | list of `Avoid`                   |

```php
use CyrildeWit\MapsUrls\Actions\Directions;
use CyrildeWit\MapsUrls\Enums\Avoid;
use CyrildeWit\MapsUrls\Enums\DirectionAction;
use CyrildeWit\MapsUrls\Enums\TravelMode;
use CyrildeWit\MapsUrls\Coordinates;

$action = new Directions(
    origin: 'Eindhoven, Nederland',
    originPlaceId: 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
    destination: 'Monnickendam, Nederland',
    destinationPlaceId: 'ChIJTZfQeLgFxkcRQhAYGf9HbrU',
    travelMode: TravelMode::Bicycling,
    directionAction: DirectionAction::Navigate,
    waypoints: ['Berlin,Germany', new Coordinates(48.8566, 2.3522)],
    avoid: [Avoid::Tolls, Avoid::Ferries],
);
```

##### Travel mode

The cases of `CyrildeWit\MapsUrls\Enums\TravelMode` are:

```php
TravelMode::Driving;
TravelMode::Walking;
TravelMode::Bicycling;
TravelMode::TwoWheeler;
TravelMode::Transit;
```

`Bicycling` is human-powered. `TwoWheeler` covers motorised two-wheelers such as motorcycles, and Google only routes it in [countries where two-wheeler directions are supported](https://developers.google.com/maps/documentation/directions/get-directions#TwoWheeledVehicles). Elsewhere the link still opens, but the mode is ignored.

##### Direction action

`CyrildeWit\MapsUrls\Enums\DirectionAction` has one case, `DirectionAction::Navigate`.

##### Place IDs

Google reads a place ID only next to the location it belongs to, so `originPlaceId` needs an `origin` and `destinationPlaceId` needs a `destination`. One on its own throws `InvalidOption`.

The origin and the destination are both optional. Leaving the origin out asks Google Maps to route from wherever the user is.

##### Waypoints and their place IDs

Google matches the two lists by position, so the first place ID belongs to the first waypoint. Leaving the place IDs out entirely is fine.

> [!WARNING]
> Passing a different number of place IDs than waypoints throws `InvalidOption`. A short list shifts every waypoint
> after it onto the wrong ID, and the route that comes back still looks plausible.

Google supports up to nine waypoints, and up to three when the link opens in a mobile browser. The package does not enforce either, since which one applies depends on where the link is opened.

```php
use CyrildeWit\MapsUrls\Actions\Directions;

$action = new Directions(
    waypoints: ['Berlin,Germany', 'Paris,France'],
    waypointPlaceIds: [
        'ChIJAVkDPzdOqEcRcDteW0YgIQQ',
        'ChIJD7fiBh9u5kcRYJSMaMOCCwQ',
    ],
);
```

##### Avoid

The cases of `CyrildeWit\MapsUrls\Enums\Avoid` are:

```php
Avoid::Ferries;
Avoid::Highways;
Avoid::Tolls;
```

Google treats these as a preference rather than a rule. A route that cannot avoid the feature is still returned.

##### Creating from an array

See [creating an action from an array](#creating-an-action-from-an-array) for the shared rules.

```php
use CyrildeWit\MapsUrls\MapsUrl;
use CyrildeWit\MapsUrls\Actions\Directions;

$directionsUrl = MapsUrl::for(Directions::fromArray([
    'origin' => 'Eindhoven, Nederland',
    'origin_place_id' => 'ChIJn8N5VRvZxkcRmLlkgWTSmvM',
    'destination' => 'Monnickendam, Nederland',
    'destination_place_id' => 'ChIJTZfQeLgFxkcRQhAYGf9HbrU',
    'travelmode' => 'driving',
    'dir_action' => 'navigate',
    'waypoints' => ['Berlin,Germany', [48.8566, 2.3522]],
    'avoid' => 'tolls',
]));
// https://www.google.com/maps/dir/?api=1&origin=Eindhoven%2C+Nederland&origin_place_id=ChIJn8N5VRvZxkcRmLlkgWTSmvM&destination=Monnickendam%2C+Nederland&destination_place_id=ChIJTZfQeLgFxkcRQhAYGf9HbrU&travelmode=driving&dir_action=navigate&waypoints=Berlin%2CGermany%7C48.8566%2C2.3522&avoid=tolls
```

#### DisplayMap

> Launch Google Maps with no markers or directions.
>
> [Google Maps URLs documentation](https://developers.google.com/maps/documentation/urls/get-started#map-action)

`CyrildeWit\MapsUrls\Actions\DisplayMap` takes:

| Argument  | Option    | Type                    |
|-----------|-----------|-------------------------|
| `center`  | `center`  | `Coordinates` or `null` |
| `zoom`    | `zoom`    | `int` or `null`         |
| `baseMap` | `basemap` | `BaseMap` or `null`     |
| `layer`   | `layer`   | `Layer` or `null`       |

Google requires the `map_action` parameter. This action always writes `map_action=map`.

```php
use CyrildeWit\MapsUrls\Actions\DisplayMap;
use CyrildeWit\MapsUrls\Enums\BaseMap;
use CyrildeWit\MapsUrls\Enums\Layer;
use CyrildeWit\MapsUrls\Coordinates;

$action = new DisplayMap(
    center: new Coordinates(-33.8569, 151.2152),
    zoom: 10,
    baseMap: BaseMap::Satellite,
    layer: Layer::Transit,
);
```

##### Zoom

Whole numbers from 0 (the whole world) to 21 (individual buildings). Anything outside that range throws `InvalidOption`. Google notes that the upper limit varies with the map data available at the location, so a zoom of 21 is not guaranteed everywhere.

##### Base map

The cases of `CyrildeWit\MapsUrls\Enums\BaseMap` are:

```php
BaseMap::Roadmap;
BaseMap::Satellite;
BaseMap::Terrain;
```

##### Layer

The cases of `CyrildeWit\MapsUrls\Enums\Layer` are:

```php
Layer::None;
Layer::Transit;
Layer::Traffic;
Layer::Bicycling;
```

> [!NOTE]
> `Layer::None` writes `layer=none`, which asks Google for a map with no layer on top. Leaving the layer unset omits
> the parameter instead. The two are not the same request, though they usually render the same map.

##### Creating from an array

See [creating an action from an array](#creating-an-action-from-an-array) for the shared rules.

```php
use CyrildeWit\MapsUrls\MapsUrl;
use CyrildeWit\MapsUrls\Actions\DisplayMap;

$displayMapUrl = MapsUrl::for(DisplayMap::fromArray([
    'center' => [-33.8569, 151.2152],
    'zoom' => 10,
    'basemap' => 'satellite',
    'layer' => 'transit',
]));
// https://www.google.com/maps/@?api=1&map_action=map&center=-33.8569%2C151.2152&zoom=10&basemap=satellite&layer=transit
```

#### StreetViewPanorama

> Launch an interactive panorama image.
>
> [Google Maps URLs documentation](https://developers.google.com/maps/documentation/urls/get-started#street-view-action)

`CyrildeWit\MapsUrls\Actions\StreetViewPanorama` takes:

| Argument     | Option      | Type                    |
|--------------|-------------|-------------------------|
| `viewpoint`  | `viewpoint` | `Coordinates` or `null` |
| `panoramaId` | `pano`      | `string` or `null`      |
| `heading`    | `heading`   | `int` or `null`         |
| `pitch`      | `pitch`     | `int` or `null`         |
| `fov`        | `fov`       | `int` or `null`         |

Google requires the `map_action` parameter. This action always writes `map_action=pano`.

Google needs somewhere to point the camera, so one of `viewpoint` and `panoramaId` has to be present. An action with neither throws `InvalidOption`. Giving both is fine: the panorama ID wins, and the viewpoint is used only when Google cannot find that panorama.

`heading` runs from -180 to 360 degrees, `pitch` from -90 to 90 and `fov` from 10 to 100. Anything outside those ranges throws `InvalidOption`.

```php
use CyrildeWit\MapsUrls\Actions\StreetViewPanorama;
use CyrildeWit\MapsUrls\Coordinates;

$action = new StreetViewPanorama(
    viewpoint: new Coordinates(48.857832, 2.295226),
    panoramaId: 'tu510ie_z4ptBZYo2BGEJg',
    heading: 120,
    pitch: 40,
    fov: 80,
);
```

##### Creating from an array

See [creating an action from an array](#creating-an-action-from-an-array) for the shared rules.

```php
use CyrildeWit\MapsUrls\MapsUrl;
use CyrildeWit\MapsUrls\Actions\StreetViewPanorama;

$panoramaUrl = MapsUrl::for(StreetViewPanorama::fromArray([
    'viewpoint' => [48.857832, 2.295226],
    'pano' => 'tu510ie_z4ptBZYo2BGEJg',
    'heading' => 120,
    'pitch' => 40,
    'fov' => 80,
]));
// https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=48.857832%2C2.295226&pano=tu510ie_z4ptBZYo2BGEJg&heading=120&pitch=40&fov=80
```

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
