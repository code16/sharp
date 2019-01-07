# Entity authorizations

We'll already reviewed authorizations for [Commands](commands.md) or [Entity States](entity-states.md). Let's see how we can define authorizations for an entity.

## Available permissions

Entities have five permission keys:

- `entity`: to see the entity in the side-menu, and to display its list. Without this, the entity is hidden to the user.
- `view`: without this, the user can access the Entity list, but not the form.
- `update`: without this, the user can see the form, but in readonly.
- `create`: without this, the user can't display the create form.
- `delete`: without this, the user can't delete an instance.


## Global authorizations

As a first step, in some cases you will want to forbid some actions to anyone: just an application rule, like "no one can delete an Order", or "no one can edit a User".

Just add the rule in a special `authorizations` key in the config:

```php
    // in config/sharp.php

    "entities" => [
        "spaceship" => [
            "list" => \App\Sharp\SpaceshipSharpList::class,
            [...]
            "authorizations" => [
                "delete" => false,
                "create" => false,
            ]
        ]
    ]
```

Note that you can't define here the `entity` permission.

## Policies

For user-based rules, create a Laravel `Policy` class which is just a plain class defining methods for some (or all) permissions.

### Write the class

```php
    class SpaceshipPolicy
    {
    
        public function entity(User $user)
        {
            return sharp_user()->hasGroup('admin');
        }
    
        public function view(User $user, $spaceshipId)
        {
            return sharp_user()->owner_id == $user->id;
        }

        public function update(User $user, $spaceshipId)
        {
            [...]
        }
        
        public function delete(User $user, $spaceshipId)
        {
            [...]
        }

        public function create(User $user)
        {
            [...]
        }
    }
```

Only write methods which don't return true, as this is the default behaviour.

### Configure the policy

This is straightforward:

```php
    // in config/sharp.php

    "entities" => [
        "spaceship" => [
            "list" => \App\Sharp\SpaceshipSharpList::class,
            [...]
            "policy" => \App\Sharp\Policies\SpaceshipPolicy::class,
        ]
    ]
```

---

> next chapter: [Multi-Forms](multiforms.md).