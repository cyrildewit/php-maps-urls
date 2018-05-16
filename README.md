# PHP Google Maps URLs

This package allows you to build URLs for the [Google Maps URLs API](https://developers.google.com/maps/documentation/urls/guide).

Here's a quick example:

```php
use CyrildeWit\MapsUrls\UrlGenerator;
use CyrildeWit\MapsUrls\Actions\SearchAction;

$searchAction = (new SearchAction())
    ->setQuery('The Netherlands Amsterdam');
$searchUrl = (new UrlGenerator($searchAction))->getUrl();

$directonAction = (new DirectionAction())
    ->setOrigin('The Netherlands Amsterdam')
    ->setDestination('The Netherlands Utrecht');
$directionUrl = (new UrlGenerator($searchAction))->getUrl();
```

## Overview

This simple PHP package will help you with generating URLs for the Google Maps URLs API. The idea was to create a fluent interface, so we can do all kind of things with it. You will likely create a wrapper class that fits your needs. There's already a wrapper created for Laravel. You can find it here: [`cyrildewit/laravel-maps-urls`](https://github.com/cyrildewit/laravel-maps-urls).

## Documentation

In this documentation, you will find some helpful information about the use of this PHP package.

### Table of contents

1. [Getting Started](#getting-started)
    * [Requirements](#requirements)
    * [Installation](#installation)
2. [Usage](#usage)

## Getting Started

### Requirements

The PHP Maps Url package required **PHP 7+**.

#### Version information

| Version | Status         | PHP Version |
|---------|----------------|-------------|
| 0.1     | Active support | >= 7.0.0    |

### Installation

You can install this package via composer using:

```winbatch
composer require cyrildewit/php-maps-urls
```

## Usage

In the following sections, you will find information about the usage of this package.
