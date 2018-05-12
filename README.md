# PHP Google Maps URLs

This package allows you to build URLs for the [Google Maps URLs API](https://developers.google.com/maps/documentation/urls/guide).

Here's a quick example:

```php
use CyrildeWit\MapsUrls\MapsUrl;
use CyrildeWit\MapsUrls\Actions\SearchAction;

$searchAction = (new SearchAction())->setQuery('The Netherlands Amsterdam');
$searchUrl = (new MapsUrl($searchAction))->getUrl();

$directonAction = (new DirectionAction())
    ->setOrigin('The Netherlands Amsterdam')
    ->setDestination('The Netherlands Utrecht');
$directionUrl = (new MapsUrl($searchAction))->getUrl();
```
