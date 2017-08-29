# Form field: Select

Class: `Code16\Sharp\Form\Fields\SharpFormSelectField`

## Configuration

### `setMultiple(bool $multiple = true)`

Allow multi-selection (default: false)

### `setClearable(bool $clearable = true)`

Allow null value in non-multiple selection (default: false)

### `setDisplayAsList()`

Display as a list (the default value):

- radio if multiple=false
- checkboxes if multiple=true

### `setDisplayAsDropdown()`

Display as a classic dropdown.

### `setMaxSelected(int $maxSelected)`

Set a maximum item selection (multiple only).
Default: unlimited.

### `setMaxSelectedUnlimited`

Unset a maximum item selection (multiple only).

### `setInline(bool $inline = true)`

Display an inline checklist (if multiple + display=list).

### `setIdAttribute(string $idAttribute)`

Set the id name attribute of options (default: "id").

## Formatter

- `toFront`: expects
	- a single id value if multiple=false
	- an array of id values OR an array of models if multiple=true
	
- `fromFront`: returns
	- a single id value if multiple=false
	- an array of arrays with the "id" key otherwise:

    [
        ["id" => 1],
        ["id" => 2]
    ]