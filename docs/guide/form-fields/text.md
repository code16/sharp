# Text

Class: `Code16\Sharp\Form\Fields\SharpFormTextField`

## Configuration

### `setInputTypeText()`

Used to set the type to regular `text` (the default).

### `setInputTypePassword()`

Used to set the type to `password`. An "eye button" is displayed to show / hide the input value.

### `setInputTypeEmail()`

Used to set the type to `email`.

### `setInputTypeTel()`

Used to set the type to `tel`.

### `setInputTypeUrl()`

Used to set the type to `url`.

### `setMaxLength(int $maxLength)`

Set a max character count.

### `setMaxLengthUnlimited()`

Unset the max character count.

### `shouldSanitizeHtml()`

Enable HTML sanitization (to prevent XSS attacks if this field data is used as raw HTML).

## Formatter

- `toFront`: expect a string.
- `fromFront`: returns a string.
