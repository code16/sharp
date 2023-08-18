# List

Class: `Code16\Sharp\Show\Fields\SharpShowListField`

This field is very similar to the [Form's File field](../form-fields/list.md), and its purpose is to display items made of other Show fields.

Here's an example, for a list of pictures with a legend:

```php
function buildShowFields()
{
    $this->addField(
        SharpShowListField::make('pictures')
            ->setLabel('additional pictures')
            ->addItemField(
                SharpShowFileField::make('file')
                    ->setStorageDisk('local')
                    ->setStorageBasePath('data/Product/{id}/pictures')
            )
            ->addItemField(
                SharpShowTextField::make('legend')
                    ->setLabel('Legend')
            )
    );
}
```

## Configuration

### `setLabel()`

Set the field label.

### `addItemField(SharpShowField $field)`

Add a SharpShowField in the item.

## Layout

The List item layout must be defined like the show itself, in the `buildShowLayout()` function. The item layout is managed as a column, with a `ShowLayoutColumn` object. To link the column and the item, use the classic `withSingleField()` function with a second argument, a Closure accepting a `ShowLayoutColumn`.

Example:

```php
$this->addColumn(6, function(ShowLayoutColumn $column) {
     $column->withSingleField('pieces', function(ShowLayoutColumn $listItem) {
          $listItem->withSingleField('acquisition_date')
              ->withSingleField('title')
              ->withSingleField('artist');
     });
 });
```

## Formatter

The Formatter expects an array or a `Collection` of models, each one defining attributes for each list item keys at the format expected by the corresponding Field Formatter.