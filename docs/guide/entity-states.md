# Entity States

Entity states are a bit of sugar to easily propose a state management on entities. It could be a simple "draft / publish" state for a page, or something more advanced with many states for an order for instance.

## Generator

```bash
php artisan sharp:make:entity-state <class_name> [--model=<model_name>]
```

## Write the Entity state class

First, you'll have to write a class that extends the `Code16\Sharp\EntityList\Commands\EntityState` abstract class.

You'll have to implement two functions: `buildStates()` and `updateState($instanceId, $stateId)`.

### Build the states

The goal is to declare the available states for the entity, using `$this->addState()`:

```php
class ProductEntityState extends EntityState
{
    // [...]
    
    protected function buildStates()
    {
        $this->addState('active', 'Active', 'green')
            ->addState('inactive', 'Retired', 'orange')
            ->addState('coming', 'Coming soon', '#ddd');
    }
}
```

`$this->addState()` takes 3 parameters:
- a key identifying the state,
- the state label as shown to the user,
- a color to display.

For the color, you may indicate anything that the browser would understand (an HTML color name or a hexadecimal value).

### Update a state

When the user clicks on a state to update it, the `updateState()` method is called.

```php
class ProductEntityState extends EntityState
{
    // [...]
    
    public function updateState($instanceId, $stateId): array
    {
        Product::findOrFail($instanceId)
            ->update(['state' => $stateId]);
    
        return $this->refresh($instanceId);
    }
}
```

About the `return $this->refresh($instanceId);`: Entity states can return either a `refresh` or a `reload` (as described in the [Commands documentation](commands.md)), but if omitted the refresh of the `$instanceId` is the default (meaning in the code sample above this line can be removed).

## Configure the state

Once the Entity state class is defined, we have to add it in the Entity List or in the Show Page config:

```php
class ProductEntityList extends SharpEntityList
{
    // [...]
    
    function buildListConfig(): void
    {
        $this->configureEntityState('state', SpaceshipEntityState::class)
    }
}
```

The first parameter is a key which should be the name of the attribute.

## Authorizations

Entity states can declare an authorization check very much like Instance Commands:

```php
class ProductEntityState extends EntityState
{
    // [...]
    
    public function authorizeFor($instanceId): bool 
    {
        return Product::findOrFail($instanceId)->owner_id == auth()->id();
    }
}
```

