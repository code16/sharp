# Textarea

Class: `Code16\Sharp\Form\Fields\SharpFormTextareaField`

## Configuration

### `setRowCount(int $rows)`

Used to set the textarea row count.

### `setMaxLength(int $maxLength)`

Set a max character count.

### `setMaxLengthUnlimited()`

Unset the max character count.

### `setSanitizeHtml()`

Enable HTML sanitization (to prevent XSS attacks if this field data is used as raw HTML).

## Formatter

- `toFront`: expect a string.
- `fromFront`: returns a string.
