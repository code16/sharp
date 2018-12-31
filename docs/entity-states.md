# Entity States

Entity states are a little bit of sugar to easily propose a state management on entities. It could be a simple "draft / publish" state for a page, or something more advanced for an order, for instance.


## Write the Entity state class

First, you'll have to write a class that extends the `Code16\Sharp\EntityList\Commands\EntityState` abstract class.

You'll have to implement two functions: `buildStates()` and `updateState($instanceId, $stateId)`.


### Build the states (`buildStates()`)

The goal is to declare the available states for the entity, using `$this->addState()`:

```php
    protected function buildStates()
    {
        $this->addState("active", "Active", "green")
            ->addState("inactive", "Retired", "orange")
            ->addState("building", "Building process", static::GRAY_COLOR)
            ->addState("conception", "Conception phase", static::DARKGRAY_COLOR);
    }
```

`$this->addState()` take 3 parameters:

- first, a key, identifting the state,
- then, the state label as shown to the user,
- and finally a color to display.

For the color, you may indicate:

- an [HTML color name](https://www.w3schools.com/colors/colors_names.asp)
- a Sharp constant, from its UI: `PRIMARY_COLOR`, `SECONDARY_COLOR`, `GRAY_COLOR`, `LIGHTGRAY_COLOR`, `DARKGRAY_COLOR`
- or an hexadecimal value, starting with `#`.


### Update a state

When the user clicks on a state to update it, this is the functional code called.

```php
    public function updateState($instanceId, $stateId)
    {
        Spaceship::findOrFail($instanceId)->update([
            "state" => $stateId
        ]);

        return $this->refresh($instanceId);
    }
```

Note the `return $this->refresh($instanceId);`: Entity states can return either a refresh or a reload (as described in the previous chapter, [Commands](commands.md)), but if omited the refresh of the `$instanceId` is the default (meaning in the code sample above this line can be deleted).


## Configure the state

Once the Entity state class is defined, we have to add it in the Entity List config:

```php
    function buildListConfig()
    {
        $this->setEntityState("state", SpaceshipEntityState::class)
        [...]
    }
```

The first parameter is a key which should be the name of the attribute.


## Authorizations

Entity states can declare an authorization check very much like Instance Commands:

```php
    public function authorizeFor($instanceId): bool {
        return Spaceship::findOrFail($instanceId)->owner_id == sharp_user()->id;
    }
```

---

> Next chapter : [Building an Entity Form](building-entity-form.md)