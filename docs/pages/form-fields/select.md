# Select

Class: `Code16\Sharp\Form\Fields\SharpFormSelectField`

![Example](./select1.gif)

## Configuration

### `self::make(string $key, array $options)`

The `$options` array can be either:

- a simple key-value array
- an array of arrays with `id` and `label` keys. Fore instance:

```php
[
    ["id"=>1, "label"=>"Label 1"],
    ["id"=>2, "label"=>"Label 2"],
]
```

This allows to write code like this:

```php
SharpFormSelectField::make("travel_id",
    Travel::orderBy("departure_date")->get()->map(function($travel) {
        return [
            "id" => $travel->id,
            "label" => $travel->departure_date->format("Y-m-d")
                . " — " . $travel->destination
        ];
    })->all()
)
```

### `setMultiple(bool $multiple = true)`

Allow multi-selection (default: false)

![Example](./select3.gif)


### `setClearable(bool $clearable = true)`

Allow null value in non-multiple selection (default: false)

### `setDisplayAsList()`

Display as a list (the default value):

- radio if multiple=false
- checkboxes if multiple=true

![Example](./select2.gif)

### `setDisplayAsDropdown()`

Display as a classic dropdown.

![Example](./select1.gif)

### `setMaxSelected(int $maxSelected)`

Set a maximum item selection (multiple only).
Default: unlimited.

### `setMaxSelectedUnlimited`

Unset a maximum item selection (multiple only).

### `setInline(bool $inline = true)`

Display an inline checklist (if multiple + display=list).

### `setIdAttribute(string $idAttribute)`

Set the id name attribute of options (default: "id").

### `setOptionsLinkedTo(string ...$fieldKeys)`

Thanks to this feature, you can link the dataset (meaning: the `options`) of the select to another field of the form (or even: to some other fields). In this case, the `options` array must be indexed with the value of the linked field.

For instance:

```php
SharpFormSelectField::make("brand",
    [
        "France" => [
            ["id"=>1, "label"=>"Renault"],
            ["id"=>2, "label"=>"Peugeot"],
        ], "Germany" => [
            ["id"=>3, "label"=>"Audi"],
            ["id"=>4, "label"=>"Mercedes"],
        ]
    ]
)->setOptionsLinkedTo("country")
```

This would work on relation with a `country` form field, which may be valued with "France" or "Germany".

In some cases you may want to depend on more than one field; you must add a nested level in the `options` array:

```php
SharpFormSelectField::make("model",
    [
        "France" => [
            1 => [["id"=>67, "label"=>"Clio"], ...],
            2 => ...
        ], "Germany" => [
            3 => [["id"=>98, "label"=>"A4"], ...],
            4 => ...
        ]
    ]
)->setOptionsLinkedTo("country", "brand")
```


## Formatter

- `toFront`: expects
	- a single id value if multiple=false
	- an array of id values OR an array of models if multiple=true

- `fromFront`: returns
	- a single id value if multiple=false
	- an array of arrays with the "id" key otherwise:

```php
[
    ["id" => 1],
    ["id" => 2]
]
```