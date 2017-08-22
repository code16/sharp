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

As we can see in this simple example, we defined two text fields giving them a mandatory `key` and an optional label. It's all there is to it, and as you've probably guessed Sharp offers several other field types, each of them having specific config. Here's the full list and documentation:

- [Text](form-fields/text.md)
- [Textarea](form-fields/textarea.md)
- [Markdown](form-fields/markdown.md)
- [Number](form-fields/number.md)
- [Html](form-fields/html.md)
- [Check](form-fields/check.md)
- [Date](form-fields/date.md)
- [Upload](form-fields/upload.md)
- [Select](form-fields/select.md)
- [Autocomplete](form-fields/autocomplete.md)
- [Tags](form-fields/tags.md)
- [List](form-fields/list.md)

### `buildFormLayout()`

Now let's build the form layout. A form layout is made of `columns`,  which contains `fields`, `lists` of fields and `fieldsets`. If needed, we can even define `tabs` above `columns`.

#### Columns and fields

Here's how we can define the layout for the simple two-fields form we built above:

    function buildFormLayout()
    {
        $this->addColumn(6, function(FormLayoutColumn $column) {
            $column->withSingleField("name")
                ->withSingleField("capacity");
        });
    }

This will result in a 50% column (columns width are 12-based, like in Entity Lists) with the 2 fields in separate rows. Note that fields are referenced with their key, previously defined in `buildFormFields()`.

Here's another possible layout, with two unequally large columns:

    function buildFormLayout()
    {
        $this->addColumn(7, function(FormLayoutColumn $column) {
            $column->withSingleField("name");
                
        })->addColumn(5, function(FormLayoutColumn $column) {
            $column->withSingleField("capacity");
        });
    }

##### Putting fields on the same row

One final way is to put fields side by side on the same column:

    function buildFormLayout()
    {
        $this->addColumn(6, function(FormLayoutColumn $column) {
            $column->withFields("name", "capacity");
        });
    }

This will align the two fields on the row. They'll have the same width (50%), but we can act on this adding a special suffix:

    $column->withFields("name|8", "capacity|4");

Once again, it's a 12-based grid, so `name` will take 2/3 of the width, and `capacity` 1/3.

##### A word on smalll screens

Columns are only used in medium to large screens (768 pixels and up).

Same for fields put on the same row: on smaller screens, they'll be placed on different rows, except if another layout is intentionally configured, using this convention:

    $column->withFields("name|8,6", "capacity|4,6");

Here, `name` will take 8/12 of the width on large screens, and 6/12 on smaller one.


#### Fieldsets

Fieldsets are useful to group some fields in a labelled block. Here's how they work:

    $this->addColumn(6, function(FormLayoutColumn $column) {
        $column->withFieldset("Details", function(FormLayoutFieldset $fieldset) {
            return $fieldset->withSingleField("name")
                            ->withSingleField("capacity");
        });
    });

"Details" is here the legend of the fieldset.


#### Lists of fields

In a `List` case, which is a form fields container [documented here](form-fields/list.md), we have to describe the list item layout. It goes like this:

    $column->withSingleField("pictures", function(FormLayoutColumn $listItem) {
        $listItem->withSingleField("file")
                 ->withSingleField("legend");
    });

Notice we added a `Closure` on a `withSingleField()` call, meaning we define a "item layout" for this field. The item is made of two fields in this example.


#### Tabs

Finally, columns can be wrapped in tabs in the form needs to be in parts. This is easy, just wrap the code:

    $this->addTab("tab 1", function(FormLayoutTab $tab) {
        $tab->addColumn(6, function(FormLayoutColumn $column) {
            $column->withSingleField("name");
            [...]
        });
    })->addTab([...])

The tab will here be labelled "tab1".


### `find($id): array`

Next, we have to write the code responsible for the instance data (in an update case). The method must return a key-value array:

    function find($id): array
    {
        return [
            "name" => "USS Enterprise",
            "capacity" => 3000
        ];
    }

As explained in [the Entity List documentation page](building-entity-list.md), Sharp provides a useful transformer trait: `Code16\Sharp\Utils\Transformers\WithCustomTransformers`, which allows this code under some circumstances detailed in the Entity List doc (in short: that your Model is implementing `Arrayable`, and even shorter: no problem with Eloquent):

    function find($id): array
    {
        return $this->setCustomTransformer("capacity", function($spaceship) {
            return $spaceship->capacity / 1000;
             
        })->transform(
            Spaceship::with("pictures")->findOrFail($id)
        );
    }

Here we applied a "custom transformer" as a Closure (we could have written a dedicated class for that, implemeting `Code16\Sharp\Utils\Transformers\SharpAttributeTransformer`) to transform `capacity`, maybe because we want the user to enter the value in thousands but our database to store it as a full number.


### `update($id, array $data)`

Well, this is the core: how to write the actual update code. Let's review two cases:

#### General case

If you are not using Eloquent (and maybe no database at all), you'll have to do it manually. Remember: Sharp aims to be as permissive as possible. So just write the code to update the instance designated by `$id` with the values in the `$data` array.

#### Eloquent case (where the magic happens)

Sharp also aims to help the applicative code to be as small as possible, and if you're using Eloquent, you can import a dedicated trait: `Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater`. And then, write this kind of code:

    function update($id, array $data)
    {
        $instance = $id ? Spaceship::findOrFail($id) : new Spaceship;


        $this->setCustomValuator("capacity", function ($spaceship, $value) {
            return $value * 1000;
            
        })->save($instance, $data);
    }

We first define a custom valuator. As for custom transformers, we can do it passing a Closure, or the full name of a class which implements `Code16\Sharp\Form\Transformers\SharpAttributeValuator`. Here we simply format back the `capacity` expressed in thousand by the user.

Then we call `$this->save()` with the instance and the sent data. This kind of magical and heavily tested method will handle all the persisting code for you, handling if needed related models (for lists, tags, selects, ...), with any relation allowed by Eloquent (hasMany, belongsToMany, morphMany, ...).

What if you want to forbid Sharp to handle automatically a specific field, for whatever reason? Well, simply call `$this->ignore("myField")` before calling `$this->save()`, and do whatever is necessary for this field after.


#### Handle applicative exceptions

In the `update($id, array $data)` method you may want to throw an exception on a special case, other than validation (which is explain below). Here's how to do that:

    function update($id, array $data)
    {
        [...]

        if($sometingIsWrong) {
            throw new SharpApplicativeException("Something is wrong");
        }
        [...]
    }

The message will be displayed to the user.


### `create(): array`

This method **is not mandatory**, a default implementation is proposed by Sharp, but you can override it if necessary. The aim is to return an array version of a new instance (for the creation form). For instance, with Eloquent and the `Code16\Sharp\Utils\Transformers\SharpAttributeTransformer` trait:

    function create(): array
    {
        return $this->transform(new Spaceship(["name" => "new"]));
    }


### `delete($id)`

Finally (!), here you must write the code performed on a deletion of the instance. It can be anything, here an Eloquent example:

    function delete($id)
    {
        Spaceship::findOrFail($id)->delete();
    }


## Configure the form

Once this class written, we have to declare the form in the sharp config file:

    // config/sharp.php
    
    return [
        "entities" => [
            "spaceship" => [
                "list" => \App\Sharp\SpaceshipSharpList::class,
                "form" => \App\Sharp\SpaceshipSharpForm::class
            ]
        ]
    ];


## Input validation

Of course you will like to have an input validation on your form. Simply create a [Laravel Form Request class](https://laravel.com/docs/5.4/validation#form-request-validation), and link it in the config:

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

---

> next chapter : [Entity authorizations](entity-authorizations.md).