# Form field: List

Class: `Code16\Sharp\Form\Fields\SharpFormListField`

A List is made of items, and each item is made of Form Fields.

Let's review a simple use case: a museum with all kind of art pieces. In the DB it's a 1-N relationship. If we choose to define a ArtPiece Entity in Sharp, we'll end up with maybe a Select, or an Autocomplete, to designate the Museum. But here, we want to do the opposite: define a Museum Entity, with an ArtPiece list.

Here how we can build this:

```php
    function buildFormFields()
    {
        $this->addField(
            SharpFormListField::make("pieces")
                ->setLabel("Art pieces")
                ->setAddable()
                ->setRemovable()
                ->addItemField(
                    SharpFormDateField::make("acquisition_date")
                        ->setLabel("Acquisition")
                )->addItemField(
                    SharpFormTextField::make("title")
                        ->setLabel("Title")
                )->addItemField(
                    SharpFormSelectField::make("artist_id", [
					    ...
    				])
                        ->setLabel("Artist")
                )
        );
    }
```

## Configuration

### `addItemField(SharpFormField $field)`

Add a SharpFormField in the item, building it like for the regular Form, with `SharpFormField::make()`.

### `setAddable(bool $addable = true)`

Defines if new items can be added to the List.
Default: false.

### `setAddText(string $addText)`

Define the text of the Add item button.
Default: "Add an item".

### `setMaxItemCount(int $maxItemCount)` and `setMaxItemCountUnlimited()`

If the List is `addable`, you can specify a maximum item count with these.
Default: unlimited.

### `setSortable(bool $sortable = true)`

Defines if items can be sorted by the user.
Default: false.

### `setOrderAttribute(string $orderAttribute)`

This is only useful when using the `WithSharpFormEloquentUpdater` trait. You can define here the name of an numerical order attribute (typically: `order`), and it will be automatically updated in the `save()` process.

### `setCollapsedItemInlineTemplate(string $template)` and `setCollapsedItemTemplatePath(string $template)`

The UI for a `sortable` List is to add a "reorder" button, which swaps the list in a readonly state. But for big List items it can be useful to define a special template for this reordering state. 
For inline template, just write the template as a string, using placeholders for data like this: `{{var}}`.

Example:

```php
    $list->setCollapsedItemInlineTemplate(
        "Foreground: <strong>{{color}}</strong>"
    )
```

For template path, give the relative path of a template file (stating in the views Laravel folder).
The template will be [interpreted by Vue.js](https://vuejs.org/v2/guide/syntax.html), meaning you can add data placeholders, DOM structure but also directives, and anything that Vue will parse. For instance:

`<div v-if="show">result is {{value}}</div>`
`<div v-else>result is unknown</div>`



### `setRemovable(bool $removable = true)`

Defines if items can be removed by the user.
Default: false.

### `setItemIdAttribute(string $itemIdAttribute)`



## Layout

The List item layout must be defined like the form itself, in the `buildFormLayout()` function. The item layout is managed as a Form column, with a `FormLayoutColumn` object. To link the column and the item, use the classic `withSingleField()` function with a second argument, a Closure accepting a `FormLayoutColumn`.

Here's an example for the Museum List defined above:

```php
    $this->addColumn(6, function(FormLayoutColumn $column) {
         $column->withSingleField("pieces", function(FormLayoutColumn $listItem) {
            $listItem->withSingleField("acquisition_date")
                     ->withSingleField("title")
                     ->withSingleField("artist_id")
         });
     });
```

## Formatter

### `toFront`

The Formatter expects an array or a `Collection` of models, each one defining attributes for each list item keys at the format expected by the corresponding Field Formatter.

So in our Museum example, we must provide an array of ArtPiece models with at least those attributes: `id`, `title`, `acquisition_date`, `artist_id`.

### `fromFront`

Returns an array with the same shape.
Newly added items will have a `null` id.