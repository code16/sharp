# Building an Entity Form

Sharp is mostly made of Entity Lists to display, search, filter, act on instances, and of Entity Forms to create or update entities.

## Write the class

As usual in Sharp, we begin by creating a class dedicated to our Entity Form and make it extend `Code16\Sharp\Form\SharpForm`; and we'll have to implement at least 5 functions:

- `buildFormFields()` and `buildFormLayout()` to build and configure the form itself,
- `find($id): array` to get the instance data,
- `update($id, array $data)` to update the instance,
- `delete($id)` to... delete the instance.

Let's see the specifics:

### `buildFormFields()`

In short, this method is meant to host the code responsible for the declaration and configuration of each form field. This must be done calling `$this->addField`:

```php
    function buildFormFields()
    {
        $this->addField(
            SharpFormTextField::make("name")
                ->setLabel("Name")

        )->addField(
            SharpFormTextField::make("capacity")
                ->setLabel("Capacity (x1000)")
        );
    }
```

As we can see in this simple example, we defined two text fields giving them a mandatory `key` and an optional label. 

#### Form fields shared attributes

Every field has the optional following setters:

- `setLabel(string $label)` for the field label displayed above it
- `setHelpMessage(string $helpMessage)` to add a help text below the field
- `setReadOnly(bool $readOnly = true)`
- `setExtraStyle(string $style)`: the CSS style will be added in a `style` attribute

In addition, all text fields have one more generic setter:

- `setPlaceholder(string $placeholder)`

#### Conditional display

The idea is to hide or show a field depending of some other field value, called "master" in this relation. To do that, use the `addConditionalDisplay(string $fieldKey, $values = true)` setter giving:
- the master `$fieldKey`, which should refer to either a Check, Select, Tags or Autocomplete field,
- the `$values` of the master field for which the "slave" field must be visible. You can put there a boolean for a Check master field, and for other fields (Select, Tags, Autocomplete), either:
	- a string value, like for instance `"red"`: the slave field is visible only when the master field value is "red"
	- a string value with a negation mark as the first char, like `"!red`": the slave field is visible only when the master field value is NOT "red"
	- an array of values: `["red", "blue"]`. The slave field is visible only when the master field value is either "red" or "blue".

You can add multiple conditional display rules, chaining calls to `addConditionalDisplay(string $fieldKey, $values = true)`. In this case, all conditions will be linked with a `AND` operator by default (meaning all conditions must be verified to display the slave field), but this can be switch to an `OR` easily with `setConditionalDisplayOrOperator()` (and back with `setConditionalDisplayAndOperator()`).

#### Formatters

Every field is linked to a Formatter, which defines the way data is formatted right before sending it to the front (last step, after transformers) and right after reception from the front (first step, before transformers).

Sharp provides a Formatter implementation per field type, but you can override this using the `setFormatter($formatter)` setter, providing a `Code16\Sharp\Form\Fields\Formatters\SharpFieldFormatter` implementation.

#### Form fields specific attributes

For the specifics of each field, here's the full list and documentation:

- [Text](form-fields/text.md)
- [Textarea](form-fields/textarea.md)
- [Markdown](form-fields/markdown.md)
- [Wysiwyg](form-fields/wysiwyg.md)
- [Number](form-fields/number.md)
- [Html](form-fields/html.md)
- [Check](form-fields/check.md)
- [Date](form-fields/date.md)
- [Upload](form-fields/upload.md)
- [Select](form-fields/select.md)
- [Autocomplete](form-fields/autocomplete.md)
- [Tags](form-fields/tags.md)
- [List](form-fields/list.md)
- [AutocompleteList](form-fields/autocomplete-list.md)
- [Geolocation](form-fields/geolocation.md)

### `buildFormLayout()`

Now let's build the form layout. A form layout is made of `columns`,  which contains `fields`, `lists` of fields and `fieldsets`. If needed, we can even define `tabs` above `columns`.

#### Columns and fields

Here's how we can define the layout for the simple two-fields form we built above:

```php
    function buildFormLayout()
    {
        $this->addColumn(6, function(FormLayoutColumn $column) {
            $column->withSingleField("name")
                ->withSingleField("capacity");
        });
    }
```

This will result in a 50% column (columns width are 12-based, like in Entity Lists) with the 2 fields in separate rows. Note that fields are referenced with their key, previously defined in `buildFormFields()`.

Here's another possible layout, with two unequally large columns:

```php
    function buildFormLayout()
    {
        $this->addColumn(7, function(FormLayoutColumn $column) {
            $column->withSingleField("name");
                
        })->addColumn(5, function(FormLayoutColumn $column) {
            $column->withSingleField("capacity");
        });
    }
```

##### Putting fields on the same row

One final way is to put fields side by side on the same column:

```php
    function buildFormLayout()
    {
        $this->addColumn(6, function(FormLayoutColumn $column) {
            $column->withFields("name", "capacity");
        });
    }
```

This will align the two fields on the row. They'll have the same width (50%), but we can act on this adding a special suffix:

```php
    $column->withFields("name|8", "capacity|4");
```

Once again, it's a 12-based grid, so `name` will take 2/3 of the width, and `capacity` 1/3.

##### A word on smalll screens

Columns are only used in medium to large screens (768 pixels and up).

Same for fields put on the same row: on smaller screens, they'll be placed on different rows, except if another layout is intentionally configured, using this convention:

```php
    $column->withFields("name|8,6", "capacity|4,6");
```

Here, `name` will take 8/12 of the width on large screens, and 6/12 on smaller one.


#### Fieldsets

Fieldsets are useful to group some fields in a labelled block. Here's how they work:

```php
    $this->addColumn(6, function(FormLayoutColumn $column) {
        $column->withFieldset("Details", function(FormLayoutFieldset $fieldset) {
            return $fieldset->withSingleField("name")
                            ->withSingleField("capacity");
        });
    });
```

"Details" is here the legend of the fieldset.


#### Lists of fields

In a `List` case, which is a form fields container [documented here](form-fields/list.md), we have to describe the list item layout. It goes like this:

```php
    $column->withSingleField("pictures", function(FormLayoutColumn $listItem) {
        $listItem->withSingleField("file")
                 ->withSingleField("legend");
    });
```

Notice we added a `Closure` on a `withSingleField()` call, meaning we define a "item layout" for this field. The item is made of two fields in this example.


#### Tabs

Finally, columns can be wrapped in tabs in the form needs to be in parts. This is easy, just wrap the code:

```php
    $this->addTab("tab 1", function(FormLayoutTab $tab) {
        $tab->addColumn(6, function(FormLayoutColumn $column) {
            $column->withSingleField("name");
            [...]
        });
    })->addTab([...])
```

The tab will here be labelled "tab1".


### `find($id): array`

Next, we have to write the code responsible for the instance data (in an update case). The method must return a key-value array:

```php
    function find($id): array
    {
        return [
            "name" => "USS Enterprise",
            "capacity" => 3000
        ];
    }
```

As for the Entity List, you'll want to transform your data before sending it. Transformers are explained in the detailled [How to transform data](how-to-transform-data.md) documentation.


### `update($id, array $data)`

Well, this is the core: how to write the actual update code. 

#### Form field format

Before going into the details, please note that the `$data` array contains the per-field formatted data: depending on the type of SharpFormField you used, the structure may change. 

For instance, a `SharpFormMarkdownField` content will be formated as an array with a `text` attribute for the full text and an optional `fields` attribute with embedded fields (see the Markdown field documentation for more details).

Sharp will use this format step to perform some tasks: move or copy uploaded files, handle image transformation, ... Note that you can override the formatter of a specific field as explained above in the `buildFormFields()` section.

Now let's review two cases:

#### General case: you are on your own

If you are not using Eloquent (and maybe no database at all), you'll have to do it manually. 

Remember: Sharp aims to be as permissive as possible. So just write the code to update the instance designated by `$id` with the values in the formatted `$data` array.

#### Eloquent case (where the magic happens) — beta

Sharp also aims to help the applicative code to be as small as possible, and if you're using Eloquent, you can import a dedicated trait: `Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater`. And then, write this kind of code:

```php
    function update($id, array $data)
    {
        $instance = $id ? Spaceship::findOrFail($id) : new Spaceship;


        $this->setCustomTransformer("capacity", function($capacity) {
                return $capacity * 1000;
            })
            ->ignore("pilots")
            ->save($instance, $data);
    }
```

We first define a custom transformer (see [detailled documentation](how-to-transform-data.md)).

Then we decide for some reason to bypass the automatic save process for the `pilots` attribute — because why not? This `ignore()` function can be called with an array as well. You'll probably do whatever is necessary for this field after the `save()` call.

Finally we call `$this->save()` with the instance and the sent data. This kind of magical, heavily tested and almost-out-of-beta method will do all the persisting crap for you, handling if needed related models (for lists, tags, selects, ...), with any relation allowed by Eloquent (hasMany, belongsToMany, morphMany, ...).


#### Handle applicative exceptions

In the `update($id, array $data)` method you may want to throw an exception on a special case, other than validation (which is explain below). Here's how to do that:

```php
    function update($id, array $data)
    {
        [...]

        if($sometingIsWrong) {
            throw new SharpApplicativeException("Something is wrong");
        }
        [...]
    }
```

The message will be displayed to the user.

#### Return the instance id

This is important for some cases (when a field formatter needs to de delayed): this method should return the id of the updated or stored instance.

#### Display notifications

Sometimes you'll want to display a message to the user, after a creation or an update. Sharp way to do this is to call `->notify()` in the Form code:

```php
    function update($id, array $data)
    {
        $instance = $id ? Spaceship::findOrFail($id) : new Spaceship;

        $this->save($instance, $data);

        $this->notify("Spaceship was indeed updated.")
             ->setDetail("As you asked.")
             ->setLevelSuccess()
             ->setAutoHide(false);

        return $instance->id;
    }
```

A notification is made of a title, and optionally 
- a texte detail,
- a notification level: info (the default), warning, danger, success,
- an auto-hide policy (if true, the toasted notification will hide after 4s).

The notification will be displayed on the next screen, which is the Entity List.

Note that you can add up notifications, calling the `notify()` function multiple times (which is useful to sometimes add a second notification, based on actual form data).

### `create(): array`

This method **is not mandatory**, a default implementation is proposed by Sharp, but you can override it if necessary. The aim is to return an array version of a new instance (for the creation form). For instance, with Eloquent and the `Code16\Sharp\Utils\Transformers\SharpAttributeTransformer` trait:

```php
    function create(): array
    {
        return $this->transform(new Spaceship(["name" => "new"]));
    }
```

### `delete($id)`

Finally (!), here you must write the code performed on a deletion of the instance. It can be anything, here's an Eloquent example:

```php
    function delete($id)
    {
        Spaceship::findOrFail($id)->delete();
    }
```

## Configure the form

Once this class written, we have to declare the form in the sharp config file:

```php
    // config/sharp.php
    
    return [
        "entities" => [
            "spaceship" => [
                "list" => \App\Sharp\SpaceshipSharpList::class,
                "form" => \App\Sharp\SpaceshipSharpForm::class
            ]
        ]
    ];
```

## Input validation

Of course you'll want to have an input validation on your form. Simply create a [Laravel Form Request class](https://laravel.com/docs/5.4/validation#form-request-validation), and link it in the config:

```php
    // config/sharp.php
    
    return [
        "entities" => [
            "spaceship" => [
                "list" => \App\Sharp\SpaceshipSharpList::class,
                "form" => \App\Sharp\SpaceshipSharpForm::class,
                "validator" => \App\Sharp\SpaceshipSharpValidator::class,
            ]
        ]
    ];
```

Sharp will handle the error display in the form.

### Validate rich text fields (markdown and wysiwyg)

Rich text fields (RTF) are structured in a certain way by Sharp. This means that a rule like this will not work out of the box, if bio is a RTF:

```php
    public function rules()
    {
        return [
            'bio' => 'required'
        ];
    }
```

To make it work, you have two options:

Either add a ".text" suffix to your field key in the rules:

```php
    public function rules()
    {
        return [
            'bio.text' => 'required'
        ];
    }
```

Or even easier, make your FormRequest class extend `Code16\Sharp\Form\Validator\SharpFormRequest` instead of `Illuminate\Foundation\Http\FormRequest`. Note that in this case, if you have to define a `withValidator($validator)` function (see the [Laravel doc](https://laravel.com/docs/5.5/validation#form-request-validation)), make sure you call `parent::withValidator($validator)` in it.

---

> next chapter: [Entity authorizations](entity-authorizations.md).