---
sidebarDepth: 3
---

# Building an Entity Show

Between an Entity List and a Form, you might want to add a Show page to display a whole instance, and allow the user to interact with it through Commands.

Note that building a Show is really optional: in fact, until Sharp 4.2, this concept didn't exist. But in some situations it could be really helpful to add this layer — and it can be even a must have when dealing with "single" resources, such as a personal account, or a configuration entity, for which it's weird to build an Entity List.

## Generator

```bash
php artisan sharp:make:show <class_name> [--model=<model_name>]
```

## Write the class

First we build a class dedicated to our Entity Show extending `Code16\Sharp\Show\SharpShow`; and we'll have to implement:

- `buildShowFields()` and `buildShowLayout()` to declare the fields presenting the instance.
- `find($id): array` to retrieve the instance.
- `buildShowConfig()` is optional, and contains Commands and Entity State declarations.

In detail:

### `buildShowFields()`

Very much like Form's `buildFormFields()`, this method is meant to host the code responsible for the declaration and configuration of each show field. This must be done by calling `$this->addField`:

```php
function buildShowFields()
{
    $this->addField(
        SharpShowTextField::make("name")
            ->setLabel("Name")

    )->addField(
        SharpShowPictureField::make("picture")
    );
}
```

#### Common attributes to all show fields

Each available Show field is detailed below; here are the attributes they all share:

##### `setShowIfEmpty(bool $show = true)`

By default, an empty field (meaning: with null or empty data) is not displayed at all in the Show UI. You can change this behaviour with this attribute.

#### Available simple Show fields

- [Text](show-fields/text.md)
- [Picture](show-fields/picture.md)
- [File](show-fields/file.md)
- [List](show-fields/list.md)


#### Embedding an Entity List in a Show

A crucial feature in the ability given to add a full Entity List in a Show, to display and interact with some "one to many" related data.

Let's see a simple example: we want to display the pilots list dedicated to a spaceship. In the spaceship Show page, we can add a pilots Entity List as a field:

```php
function buildShowFields()
{
    SharpShowEntityListField::make("pilots", "pilot");
}
```

Sharp will consider this as a regular Entity List, meaning it will look for a `sharp.entities.pilot.list` config key containing an EntityList class name, build it and display it it the Show as a field (see below for layout), with the full feature set of an Entity List: filters, commands, reorder, entity state, search, ...

Clicking a row in the EntityList can lead to whatever you've configured for this entity: a Show or a Form. Sharp will maintain a navigation breadcrumb under the hood to, in this case, get back to the spaceship Show after a pilot update or creation.

Notice that you have three possibilities for the actual code of this EntityList: 
- if want to have a "pilots" entity in the main menu, you can reuse the same EntityList instance for the Show (and configure it to scope the data, as we'll discuss below),
- or you can build a specific EntityList without declaring it in the main menu,
- or you can have both, making the spaceship Show version of the  pilots EntityList extend the other.

As always with Sharp, implementation is up to you.

But at this stage we still have a major issue: how to scope the data of the EntityList? In our case, we want to display and interact only with pilots linked to our spaceship... For this and more on personalization, refer to the detailed documentation of this field:

- [embedded EntityList](show-fields/embedded-entity-list.md)


### `buildShowLayout()`

The show layout is a simplifed version of the Form layout, and is made of sections which contains `columns` of `fields`.

#### Sections

A section is just a block of fields, packed in columns:

```php
function buildShowLayout()
{
    $this->addSection(
        'Description', 
        function(ShowLayoutSection $section) {
            ...
        }
    );
}
```


#### Columns and fields

Just like for Forms, a `ShowLayoutSection` is made of columns and fields. So completing the example above:

```php
function buildShowLayout()
{
    $this->addSection(
        'Description', 
        function(ShowLayoutSection $section) {
            $section->addColumn(
                9, 
                function(ShowLayoutColumn $column) {
                    $column->withSingleField("description");
                }
            );
        }
    );
}
```

A `ShowLayoutColumn`, very much like a `FormLayoutColumn`, can declare single field rows and multi fields rows. Report to the [Form layout documentation](building-entity-form.md#buildformlayout) to find out how.

#### `SharpShowListField`'s layout

Like `SharpFormListField` in Forms, a `SharpShowListField` must declare its item layout, in order to describe how fields are displayed, like in this example:

```php
function(ShowLayoutSection $section) {
    $section->addColumn(9, 
        function(ShowLayoutColumn $column) {
             $column->withSingleField("pictures", function(ShowLayoutColumn $listItem) {
                  // Notice that the list item layout is just a ShowLayoutColumn
                  $listItem
                      ->withSingleField("file")
                      ->withSingleField("legend");
             });
        }
    );
}
```

#### Embedded Entity Lists

An embedded Entity List in treated as a special section; its label will be displayed as section title. 

```php
function buildShowLayout()
{
    $this->addEntityListSection('members');
}
```


### `find($id): array`

As for Forms, the method must return a key-value array:

```php
function find($id): array
{
    return [
        "name" => "USS Enterprise",
        "capacity" => 3000
    ];
}
```

And as for Forms, you'll want to transform your data before sending it. 

```php
function find($id): array
{
	return $this
		->setCustomTransformer(
		    "name", 
		    function($value, $spaceship) {
			    return strtoupper($spaceship->name);
		    }
		)
		->setCustomTransformer(
		    "picture", 
		    new SharpUploadModelThumbnailUrlTransformer(600)
		);
}
```


Transformers are explained in the detailled [How to transform data](how-to-transform-data.md) documentation.


### `buildShowConfig()`

Very much like EntityLists, a Show can declare a config with (instance only) `Commands` and `EntityState` handlers:

```php
function buildShowConfig()
{
   $this
        ->addInstanceCommand("message", SpaceshipSendMessage::class)
        ->addInstanceCommandSeparator()
        ->addInstanceCommand("external", SpaceshipExternalLink::class)
        ->setEntityState("state", SpaceshipEntityState::class);
}
```

Refer [to Commands](commands.md) (keep in mind only `InstanceCommand`s are possible in a Show) and [EntityState](entity-states.md) documentations.


## Accessing the navigation breadcrumb

A common pattern for Shows is to add an embedded EntityList with related entities, and to allow update but also creation from there. Taking back our spaceship / pilots example, we may need to add a pilot to the spaceship. Question is: how can we attach a newly created pilot to a spaceship? 

Answer is: accessing the navigation breadcrumb, with [SharpContext](context.md), and more precisely with its `getPreviousPageFromBreadcrumb()` method. Here's a full example:

```php
class PilotSharpForm extends SharpForm
{
    use WithSharpFormEloquentUpdater, WithSharpContext;

    [...]

    function update($id, array $data)
    {
        $pilot = $id ? Pilot::findOrFail($id) : new Pilot;
        $pilot = $this->save($pilot, $data);

        if($this->context()->isCreation()) {
            if($breadcrumb = $this->context()->getPreviousPageFromBreadcrumb("show")) {
                list($type, $entityKey, $instanceId) = $breadcrumb;
                if ($entityKey == "spaceship") {
                    Spaceship::findOrFail($instanceId)
                        ->pilots()
                        ->attach($pilot->id);
                }
            }
        }
    }
}
```
