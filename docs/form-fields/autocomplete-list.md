# Form field: AutocompleteList

Class: `Code16\Sharp\Form\Fields\SharpFormAutocompleteListField`

This one is seems a little strange. The goal is to build a List with only one field in each item, an Autocomplete.

First let's see a use case: imagine you want to handle a list of `winners` by selecting them in a big list of Players, for which an remote Autocomplete is the best choice (otherwise you could have opted for a Tags Field).

You can in fact define the list as this:

```php
    SharpFormAutocompleteListField::make("winners")
            ->setLabel("Winners")
            ->setItemField(
                SharpFormAutocompleteField::make("item", "remote")
                    ->setRemoteEndpoint("/players")
                    [...]
            )
    );
```

> Note that the key of the Autocomplete, `item` here, could be anything you want, as soon you stay consistent in the `buildFormLayout()` part.

But why can't we use a classic List for this? Well, the `model->winners` relation is N-N, here (`belongsToMany`), but Lists are meant to handle 1-N relationships (`hasMany`). Here we want one field, the Autocomplete, to represent the whole item object.


## Configuration

Configuration is the same as the classic [List](list.md), except for:

### `setItemField(SharpFormAutocompleteField $field)`

You can use this function instead of `addItemField`, since items of AutocompleteList have only one field.

### `addItemField(SharpFormField $field)`

Simply know that this method is an alias for `setItemField()`, meaning that y ou can only pass an Autocomplete, and it can only be called once.


## Formatter

### `toFront`

Well, you must provide an array or Collection (same as for a List, see [related documentation](list.md)) of models with at least attributes designated by templates for the Autocomplete (see [related documentation](autocomplete.md)).

### `formFront`

Returns the selected item id.