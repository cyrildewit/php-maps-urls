# Release Notes

All notable changes to `PHP Google Maps URLs` will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [v2.0.0]

Actions are now immutable and built through their constructor with named arguments. Everything that existed to configure an action after construction has gone, which is most of the package. Upgrading means rewriting the call sites, and the sections below list what each one becomes.

### Added

- Added the `Action` interface, which every action implements. It has two methods, `endpoint()` and `parameters()`
- Added `MapsUrl::for()`, which builds a URL from an action without any tracking parameters
- Added the `Coordinates` value object, accepted by every parameter that takes a position. It formats the pair with `sprintf()` at seven decimals, rejects a latitude outside -90 to 90 and a longitude outside -180 to 180, and offers `Coordinates::unchecked()` for a longitude that wraps past the antimeridian
- Added the `Enums\Avoid` enum, the `avoid` option and the `avoid` constructor argument on `Actions\Directions`
- Added the `two-wheeler` case to `Enums\TravelMode`
- Added `utm_source` and `utm_campaign` support to `UrlGenerator`, which Google asks every URL to carry
- Added the `Exceptions\InvalidOption` exception, which replaces the seven per-option exception classes
- Added range validation to the zoom, which now rejects anything outside 0 to 21
- Added a check that the number of waypoint place IDs matches the number of waypoints. Google matches the two lists by position, so a short list shifted every waypoint after it onto the wrong ID
- Added a guard against an action writing `api`, `utm_source` or `utm_campaign`. Those belong to `UrlGenerator`, and an action returning one used to override it, producing a URL without the `api=1` that Google requires
- Added the required-parameter rules Google documents. `Actions\Search` takes the query as a required argument, `Actions\StreetViewPanorama` rejects an action with neither a viewpoint nor a panorama ID, and `Actions\Directions` rejects a place ID without the origin or destination it belongs to. Each of these produced a URL that Google could not read
- Added `declare(strict_types=1)` and native type declarations across the package

### Changed

- Raised the minimum PHP version to `^8.5`
- Relicensed the package from Apache-2.0 to MIT
- Renamed the action classes. `Actions\SearchAction` is now `Actions\Search`, `Actions\DirectionsAction` is now `Actions\Directions`, `Actions\DisplayMapAction` is now `Actions\DisplayMap` and `Actions\DisplayStreetViewPanoramaAction` is now `Actions\StreetViewPanorama`
- Actions are `final readonly` and take every parameter through their constructor. All setters and getters are gone, and the parameters are public properties you can read back
- Replaced `Actions\AbstractAction` with the `Action` interface. An action no longer inherits behaviour, so a parameter Google adds before the package catches up can be supported by writing a class that implements `Action`
- Renamed `make()` to `fromArray()`, and it now reports a value of the wrong type as an `InvalidOption` rather than letting a `TypeError` escape
- `fromArray()` accepts a `[latitude, longitude]` array anywhere a position is accepted, including `query`, `origin`, `destination` and `waypoints`. A keyed array is rejected, because reading it in the wrong order swaps the two and still produces a URL that loads
- `fromArray()` rejects an option it has no argument for instead of skipping it silently
- `fromArray()` resolves string enum options with `tryFrom()`, so a value now has to match the case exactly. `'DRIVING'` and `'Driving'` were accepted before because the value was lowercased first
- `UrlGenerator` takes the action as an argument to `generate()` rather than holding it. It is `final readonly`, so one instance is safe to share
- The campaign is a per-link argument to `generate()`, with an optional default on the constructor. Google describes `utm_campaign` as the intent behind one link, so it varies where `utm_source` does not
- Converted `Enums\BaseMap`, `Enums\DirectionAction`, `Enums\Layer` and `Enums\TravelMode` from constant classes to native backed enums, so `TravelMode::DRIVING` becomes `TravelMode::Driving`
- The parameters for those options take an enum instance, and the matching properties hold one
- Coordinates are formatted with `sprintf()` at seven decimals rather than string interpolation, which honoured the `precision` ini setting and switched to exponential notation below `1e-4`
- The center and the viewpoint are a single `Coordinates` argument rather than a latitude and a longitude that could be set apart from each other
- The zoom is read back as `int` rather than the `string` it was cast to
- Migrated the test suite from PHPUnit to [Pest](https://pestphp.com/) and added PHPStan, Pint and Rector (development only; no impact on consumers)

### Fixed

- Fixed the directions endpoint, which produced `/maps/dir?api=1` instead of the documented `/maps/dir/?api=1`
- Fixed the `pano` option, which mapped to a `setPano()` method that does not exist, so building a panorama action from an array always failed
- Fixed a center or viewpoint on the equator or the prime meridian being dropped from the URL, because `empty()` read a `0.0` coordinate as absent
- Corrected the `BaseMap` cases to `roadmap`, `satellite` and `terrain`. The old `none`, `traffic` and `bicycling` values belong to the `layer` option and were never valid base maps
- Raised the Street View pitch upper bound from 80 to 90 degrees, matching the documented range
- Corrected the out-of-range messages, which reported 180 to 360 for a heading range of -180 to 360 and -10 to 100 for a fov range of 10 to 100

### Removed

- Removed every setter and getter on the actions and on `UrlGenerator`, including `setAction()`, `hasWaypoints()`, `hasWaypointPlaceIds()` and `hasAvoid()`
- Removed `Actions\AbstractAction`
- Removed the `Exceptions\InvalidBaseMap`, `InvalidDirectionAction`, `InvalidFov`, `InvalidHeading`, `InvalidLayer`, `InvalidPitch` and `InvalidTravelMode` exceptions in favour of `Exceptions\InvalidOption`
- Removed the `query_coordinates` option, which `Coordinates` replaces
- Removed `setCenterLatitude()`, `setCenterLongitude()`, `setViewpointLatitude()` and `setViewpointLongitude()`, which made half a coordinate pair representable
- Removed the `$travelModes`, `$directionActions`, `$baseMaps` and `$layers` allow-list properties and the `$queryParametersSetters` and `$queryParametersEnums` maps, which the enums and the constructors make redundant

## [v1.0.1]

### Changed

- Add support for PHP 8.2, 8.3 and 8.4
- Replaced the abbreviated license text with the full Apache 2.0 license

## [v1.0.0]

### Changed

- Raised the minimum PHP version to `^7.4`
- Read action state through getters rather than the properties directly

### Removed

- Removed the `setApiVersion()` setter from `UrlGenerator`

## [v0.1.0]

Initial release.
