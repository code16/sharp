# Form field: List

Class: `Code16\Sharp\Form\Fields\SharpFormListField`

## Configuration


`<item>` special case

Sometimes you'll want to refer the whole related object for a list item. The solution is to use `<item>` as field key, and Sharp will then pass the object to the Field Formatter.

Let's review a use case: imagine you want to handle a list of `winners` by selecting them in a big list of Players, for which an remote Autocomplete is the best choice (otherwise you could have opted for a Tags Field).

The `model->winners` relation is N-N, here (`belongsToMany`), but Lists are meant to handle 1-N relationships (`hasMany`).

You can in fact define the list as this:

    SharpFormListField::make("winners")
            ->setLabel("Winners")
            ->addItemField(
                SharpFormAutocompleteField::make("<item>", "remote")
                    ->setRemoteEndpoint("/players")
                    [...]
            )
    );

Because of this special `<item>` key, Sharp will replace each item .

Please note this special case implies that 

- you only have ONE field in the list item, since it represents the whole item.
- The List's `setItemIdAttribute` must match the Autocomplete's `setItemIdAttribute`.


## Formatter

