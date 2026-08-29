# Release Notes

All notable changes to `PHP Google Maps URLs` will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [v2.0.0]

### Added

- Added the `ValueObjects\Coordinates` value object, accepted by `SearchAction::setQuery()`, `DirectionsAction::setOrigin()`, `setDestination()` and `setWaypoints()`, `DisplayMapAction::setCenter()` and `DisplayStreetViewPanoramaAction::setViewpoint()`
- Added the `Enums\Avoid` enum, the `avoid` option and `DirectionsAction::setAvoid()`, `getAvoid()` and `hasAvoid()`
- Added the `two-wheeler` case to `Enums\TravelMode`
- Added `UrlGenerator::setUtmSource()` and `setUtmCampaign()`, which append the `utm_source` and `utm_campaign` parameters Google asks every URL to carry
- Added the `Exceptions\InvalidOption` exception, which replaces the seven per-option exception classes
- Added range validation to `DisplayMapAction::setZoom()`, which now rejects anything outside 0 to 21
- Added `declare(strict_types=1)` and native type declarations across the package

### Changed

- Raised the minimum PHP version to `^8.5`
- Relicensed the package from Apache-2.0 to MIT
- Converted `Enums\BaseMap`, `Enums\DirectionAction`, `Enums\Layer` and `Enums\TravelMode` from constant classes to native backed enums, so `TravelMode::DRIVING` becomes `TravelMode::Driving`
- The setters for those options now take an enum instance instead of a string, and the matching getters return an enum instance instead of a string
- `make()` now rejects an option it has no setter for instead of skipping it silently
- `make()` resolves string enum options with `tryFrom()`, so a value now has to match the case exactly. `'DRIVING'` and `'Driving'` were accepted before because the value was lowercased first
- `make()` spreads an array option over a setter that takes more than one parameter, so `make(['center' => [52.09, 5.12]])` reaches `setCenter($latitude, $longitude)` instead of raising a `TypeError`
- `SearchAction::setQuery()` accepts a `Coordinates` instance in place of the `query_coordinates` option
- `DisplayMapAction::setCenter()` and `DisplayStreetViewPanoramaAction::setViewpoint()` take either a `Coordinates` instance or a latitude and a longitude. Passing a float latitude without a longitude throws `InvalidOption`
- `DisplayMapAction::getZoom()` returns `?int` rather than the `?string` it cast the zoom to
- Coordinates are formatted with `sprintf()` at seven decimals rather than string interpolation, which honoured the `precision` ini setting and switched to exponential notation below `1e-4`
- Migrated the test suite from PHPUnit to [Pest](https://pestphp.com/) and added PHPStan, Pint and Rector (development only; no impact on consumers)

### Fixed

- Fixed the directions endpoint, which produced `/maps/dir?api=1` instead of the documented `/maps/dir/?api=1`
- Fixed the `pano` option, which mapped to a `setPano()` method that does not exist, so `DisplayStreetViewPanoramaAction::make(['pano' => '...'])` always failed. It maps to `setPanoramaId()` now
- Fixed a center or viewpoint on the equator or the prime meridian being dropped from the URL, because `empty()` read a `0.0` coordinate as absent
- Corrected the `BaseMap` cases to `roadmap`, `satellite` and `terrain`. The old `none`, `traffic` and `bicycling` values belong to the `layer` option and were never valid base maps
- Raised the Street View pitch upper bound from 80 to 90 degrees, matching the documented range
- Corrected the out-of-range messages, which reported 180 to 360 for a heading range of -180 to 360 and -10 to 100 for a fov range of 10 to 100

### Removed

- Removed the `Exceptions\InvalidBaseMap`, `InvalidDirectionAction`, `InvalidFov`, `InvalidHeading`, `InvalidLayer`, `InvalidPitch` and `InvalidTravelMode` exceptions in favour of `Exceptions\InvalidOption`
- Removed the `query_coordinates` option and `SearchAction::setQueryCoordinates()`
- Removed the `DirectionsAction::invalidTravelMode()` and `invalidDirectionAction()` and `DisplayMapAction::invalidBaseMap()` and `invalidLayer()` guards, which the enums make redundant
- Removed the `$travelModes`, `$directionActions`, `$baseMaps` and `$layers` allow-list properties, replaced by the `$queryParametersEnums` map

## [v1.0.1]

### Changed

- Add support for PHP 8.2, 8.3 and 8.4
- Replaced the abbreviated license text with the full Apache 2.0 license

## [v1.0.0]

### Changed

- Raised the minimum ~~~~PHP version to `^7.4`
- Read action state through getters rather than the properties directly

### Removed

- Removed the `setApiVersion()` setter from `UrlGenerator`

## [v0.1.0]

Initial release.
