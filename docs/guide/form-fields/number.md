# Number

Designate a numeric textfield.
Class: `Code16\Sharp\Form\Fields\SharpFormNumberField`

## Configuration

### `setMin(float $min)`

The minimum value that the UI allows.

### `setMax(float $max)`

The maximum value that the UI allows.

### `setStep(float $step)`

The step between values (with controls or arrow keys).
Default is 1.

### `setShowControls(bool $showControls = true)`

Display mouse control (spinner).
Default is false.


## Formatter

- `toFront`: will cast the provided value as a float.
- `fromFront`: returns a float.