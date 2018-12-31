# Form field: Tags

Class: `Code16\Sharp\Form\Fields\SharpFormTagsField`

## Configuration

### `setCreatable(bool $creatable = true)`

If true, the user can create a new value from the form. 
Default: false.

### `setCreateText(string $createText)`

The text displayed to the user when creating a new value.
Default: "Create"

### `setCreateAttribute(string $attribute)`

The name of the attribute which should be used for the creation.

### `setIdAttribute(string $idAttribute)`

Set the id name attribute of tags.
Default: "id"

### `setDisplayAsDropdown()`

Display as a classic dropdown.

### `setMaxTagsCount(int $maxTagCount)`

Set a maximum tags selection.
Default: unlimited.

### `setMaxTagCountUnlimited`

Unset a maximum tags selection.

### `setInline(bool $inline = true)`

Display an inline checklist (if multiple + display=list).


## Formatter

- `toFront`: expects an array of id values OR an array of models.
	
- `fromFront`: returns an array of arrays with the "id" key, and the "createAttribute" key in creation case:

```php
    [
        ["id" => 1],
        ["id" => null, "name" => "Bob Marley]
    ]
```

In this example, the user selected one tag and created another one with the "Bob Marley" text.