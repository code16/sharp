# Form field: Geolocation

A map-based field to pick a precise location and return its coordinates (latitude and longitude)
Class: `Code16\Sharp\Form\Fields\SharpFormGeolocationField`

## Configuration

### `setDisplayUnitDegreesMinutesSeconds()`

Sets the coordinate display to be degrees-minutes-second, eg: `17°10'16'', 89°17'45''`

### `setDisplayUnitDecimalDegrees()`

Sets the coordinate display to be decimal degrees, eg: 
`0.36666667, 17.15722222`. 
This is the default.

### `setGeocoding(bool $geocoding = true)`

Autorize geocoding, meaning enter an address and get back the coordinates.
Default is false. Require an api key (see below).

### `setApiKey(string $apiKey)`

A valid Google Maps Api key, needed for geocoding.


## Formatter

- `toFront`: expects a string with comma-separated decimal degrees  values (`0.36666667,17.15722222` for instance).
- `fromFront`: returns a string with the same format than `toFront`.