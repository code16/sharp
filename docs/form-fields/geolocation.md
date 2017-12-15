# Form field: Geolocation

A map-based field to pick a precise location and return its coordinates (latitude and longitude)

Class: `Code16\Sharp\Form\Fields\SharpFormGeolocationField`

![Example](geolocation.gif)

## Configuration

### `setDisplayUnitDegreesMinutesSeconds()`

Sets the coordinate display to be degrees-minutes-second, eg: `17°10'16'', 89°17'45''`

### `setDisplayUnitDecimalDegrees()`

Sets the coordinate display to be decimal degrees, eg: 
`0.36666667, 17.15722222`. 
This is the default.

### `setInitialPosition(float $lat, float $lng)` and `clearInitialPosition()`

Sets the initial position of the edit map, when there in no marker yet.

### `setBoundaries(float $northEastLat, float $northEastLng, float $southWestLat, float $southWestLng)` and `clearBoundaries()`

If needed, set boundaries to the edit map, providing a north-east and a south-west position.

### `setZoomLevel(int $zoomLevel)`

Set the map zoom level, from 1 (the World) ou 25. Default is 10.

### `setGeocoding(bool $geocoding = true)`

Autorize geocoding, meaning enter an address and get back the coordinates.
Default is false. Require an api key (see below).

### `setApiKey(string $apiKey)`

A valid Google Maps Api key, needed for geocoding.


## Formatter

- `toFront`: expects a string with comma-separated decimal degrees  values (`0.36666667,17.15722222` for instance).
- `fromFront`: returns a string with the same format than `toFront`.