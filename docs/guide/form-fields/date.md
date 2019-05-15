# Date

Class: `Code16\Sharp\Form\Fields\SharpFormDateField`

![Example](./date.gif)

## Configuration

### `setHasDate($hasDate = true)`

Let the user enter a date (default is true).

### `setHasTime($hasTime = true)`

Let the user enter a time (default is false).

### `setMondayFirst(bool $mondayFirst = true)`

Put monday as the first day in the calendar (default: false).

### `setSundayFirst(bool $sundayFirst = true)`

Put sunday as the first day in the calendar (default: true).

### `setMinTime(int $hours, int $minutes = 0)`
### `setMaxTime(int $hours, int $minutes = 0)`

If set, the time-chooser will be constraint as defined.

### `setStepTime(int $step)`

Set a time step (in minutes) for the time-chooser. Default is 30.

### `setDisplayFormat(string $displayFormat)`

Set the date / time display format in the field, as defined by [moment.js](https://momentjs.com/docs/#/parsing/string-format/). Default is `YYYY-MM-DD`.


## Formatter

- `toFront`: accept either a Carbon instance, a DateTime instance of a "Y-m-d H:i:s" string.
- `fromFront`: return a "Y-m-d H:i:s" string.