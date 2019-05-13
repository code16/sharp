# Number

Designate a integer-only textfield.
Class: `Code16\Sharp\Form\Fields\SharpFormNumberField`

## Configuration

### `setMin(int $min)`

The minimum value that the UI allows.

### `setMax(int $max)`

The maximum value that the UI allows.

### `setStep(int $step)`

The step between values (with controls or arrow keys).
Default is 1.

### `setShowControls(bool $showControls = true)`

Display mouse control (spinner).
Default is false.


## Formatter

- `toFront`: will cast the provided value as an int.
- `fromFront`: returns an int.