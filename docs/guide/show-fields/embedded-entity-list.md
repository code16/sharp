# Embedded EntityList

Class: `Code16\Sharp\Show\Fields\SharpShowEntityListField`

## Constructor

This field needs a local field key and the EntityList key as defined in a `sharp.entities.{key}.list` config key containing an EntityList class name. 

For instance:

```php
SharpShowEntityListField::make("cakes", "cake")
```

where `cakes` is the field key and `cake` is the related EntityList key.

Embedded EntityList are just regular EntityList presented in a Show page: we therefore need a full EntityList implementation linked in the config. To scope the data to the `instance` of the Show, use `hideFilterWithValue()` (see below).

Note that the local field key is only used for the layout: unlike every other field, the `instance` of the Show don't have to expose an attribute named like that, since the EntityList data is gathered with a dedicated request.


## Configuration


### `hideFilterWithValue(string $filterName, $value)`

This is maybe the most important method of the field, since it will not only hide a filter, but also set its value. The purpose is to allow to scope the data to the instance of the Show. For instance, let's say we display a Spaceship and that we want to embed a list of pilots:

```php
SharpShowEntityListField::make("pilots", "spaceship_pilot")
    ->hideFilterWithValue("spaceship", 12);
```

We defined here that we want a `pilots` fields related to an EntityList which implementation class is defined in the `sharp.entities.spaceship_pilot.list` config key, and its `spaceship` filter (which must be declared as usual in the EntityList implementation) must be hidden AND valued to `12` when gathering the data. In short: we want the pilots for the spaceship of id `12`.

You can pass a Closure as the value, and it will contain the current Show instance id. In most cases, you'll have to write this:

```php
SharpShowEntityListField::make("pilots", "spaceship_pilot")
    ->hideFilterWithValue(
        "spaceship", 
        function($instanceId) {
            return $instanceId;
        }
    );
```


### `hideEntityCommand($commands)`

Use it to hide any entity command in this particular embedded EntityList (useful when reusing an EntityList).  
This will apply before looking at atutorisations.

### `hideInstanceCommand($commands)`

Use it to hide any instance command in this particular embedded EntityList (useful when reusing an EntityList).  
This will apply before looking at atutorisations.

### `showEntityState(bool $showEntityState = true)`

Use it to show or hide the EntityState label and command (useful when reusing an EntityList).  
This will apply before looking at atutorisations.

### `showCreateButton(bool $showCreateButton = true)`

Use it to show or hide the create button in this particular embedded EntityList (useful when reusing an EntityList).  
This will apply before looking at atutorisations.

### `showReorderButton(bool $showReorderButton = true)`

Use it to show or hide the reorder button in this particular embedded EntityList (useful when reusing an EntityList).  
This will apply before looking at atutorisations.

### `showSearchField(bool $showSearchField = true)`

Use it to show or hide the search field in this particular embedded EntityList (useful when reusing an EntityList).  
This will apply before looking at atutorisations.


## Transformer

There is no transformer, since Sharp will NOT look for an attribute in the instance sent. The data of the EntityList is brought by a distinct XHR call, the same Sharp will use for a non embedded EntityList.


## A note on extending existing EntityList

You can of course build a dedicated EntityList, but in many cases it's useful to extend one already needed in the main menu. In our stupid spaceship / pilots example, for instance, we can have a full pilots EntityList, presented in the main menu, with all pilots and some general commands, and another one for the spaceship Show, which extends the first one and maybe redefine its columns layout, or allow reorder because it had sense in this case. This is what's been done in [Sharp's Saturn demo project](https://sharp.code16.fr/sharp/).