# Form field: List

Class: `Code16\Sharp\Form\Fields\SharpFormListField`

## Configuration


`<item>` special case

Sometimes you'll want to refer the whole related object for a list item. The solution is to use `<item>` as field key, and Sharp will then pass the object to the Field Formatter.

Let's review a use case: imagine you want to handle a list of `winners`, selecting them in a big list of Players, for which an remote Autocomplete is the best choice. You can define the field as this:

    SharpFormListField::make("winners")
            ->setLabel("Winners")
            ->addItemField(
                SharpFormAutocompleteField::make("<item>", "remote")
                    ->setLabel("Player")
            )
    );

Then Sharp will grab, by default, `id` and `label` attributes from the `Player` object in the Autocomplete Formatter (see [related documentation](autocomplete.md)).


## Formatter

